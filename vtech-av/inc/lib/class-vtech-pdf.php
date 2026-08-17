<?php
/**
 * VTECH minimal self-contained PDF writer (no external libraries).
 *
 * Generates a real, downloadable PDF for quotes/invoices using core PDF
 * primitives (Helvetica base-14 fonts). Supports headings, paragraphs, a
 * key/value block and a line-item table. Not a full HTML renderer — it is a
 * purpose-built document composer for VTECH quotes & invoices.
 *
 * @package VTECH_AV
 */
if ( ! defined( 'ABSPATH' ) ) { exit; }

class VTECH_PDF {
	private $objects = array();
	private $pages = array();
	private $buf = '';
	private $y;
	private $page_h = 842;   // A4 points
	private $page_w = 595;
	private $margin = 48;
	private $lines = array(); // queued content ops for current page
	private $primary = array( 0.505, 0.0, 0.498 ); // #81007F

	public function __construct() { $this->y = $this->page_h - $this->margin; }

	private function esc( $t ) { return str_replace( array( '\\', '(', ')' ), array( '\\\\', '\\(', '\\)' ), $t ); }

	private function ensure_space( $need = 16 ) {
		if ( $this->y - $need < $this->margin ) { $this->new_page(); }
	}
	public function new_page() {
		if ( $this->lines ) { $this->pages[] = implode( "\n", $this->lines ); $this->lines = array(); }
		$this->y = $this->page_h - $this->margin;
	}
	public function text( $t, $size = 11, $bold = false, $color = null, $x = null ) {
		$this->ensure_space( $size + 6 );
		$x = null === $x ? $this->margin : $x;
		$font = $bold ? '/F2' : '/F1';
		$c = $color ?: array( 0, 0, 0 );
		$this->lines[] = sprintf( 'q %0.3f %0.3f %0.3f rg BT %s %d Tf %d %d Td (%s) Tj ET Q', $c[0], $c[1], $c[2], $font, $size, $x, $this->y, $this->esc( $t ) );
		$this->y -= $size + 6;
	}
	public function heading( $t ) { $this->text( $t, 15, true, $this->primary ); $this->y -= 2; }
	public function spacer( $h = 10 ) { $this->y -= $h; }
	public function rule() {
		$this->ensure_space( 8 );
		$this->lines[] = sprintf( 'q %0.3f %0.3f %0.3f RG 1 w %d %d m %d %d l S Q', $this->primary[0], $this->primary[1], $this->primary[2], $this->margin, $this->y, $this->page_w - $this->margin, $this->y );
		$this->y -= 10;
	}
	// A 4-column line-item row: desc | qty | unit | amount
	public function row( $c1, $c2, $c3, $c4, $bold = false ) {
		$this->ensure_space( 16 );
		$font = $bold ? '/F2' : '/F1';
		$cols = array( $this->margin, 360, 430, 500 );
		$vals = array( $c1, $c2, $c3, $c4 );
		$op = 'q 0 0 0 rg BT ' . $font . ' 10 Tf';
		foreach ( $vals as $i => $v ) {
			$op .= sprintf( ' 1 0 0 1 %d %d Tm (%s) Tj', $cols[ $i ], $this->y, $this->esc( (string) $v ) );
		}
		$op .= ' ET Q';
		$this->lines[] = $op;
		$this->y -= 15;
	}
	public function output( $filename, $download = true ) {
		$this->new_page(); // flush last page
		// Build PDF objects.
		$n = 0; $objs = array();
		$font1 = "<< /Type /Font /Subtype /Type1 /BaseFont /Helvetica >>";
		$font2 = "<< /Type /Font /Subtype /Type1 /BaseFont /Helvetica-Bold >>";
		$kids = array(); $content_ids = array();
		$total_objs = 2 + count( $this->pages ) * 2 + 2; // catalog, pages, per-page (page+content), 2 fonts
		// We'll assign ids sequentially.
		$id = 1;
		$catalog_id = $id++; $pages_id = $id++;
		$font1_id = $id++; $font2_id = $id++;
		$page_ids = array(); $content_ids = array();
		foreach ( $this->pages as $c ) { $page_ids[] = $id++; $content_ids[] = $id++; }

		$out = "%PDF-1.4\n"; $offsets = array();
		$emit = function ( $oid, $body ) use ( &$out, &$offsets ) { $offsets[ $oid ] = strlen( $out ); $out .= "$oid 0 obj\n$body\nendobj\n"; };

		$emit( $catalog_id, "<< /Type /Catalog /Pages $pages_id 0 R >>" );
		$kids_str = implode( ' ', array_map( function ( $p ) { return "$p 0 R"; }, $page_ids ) );
		$emit( $pages_id, "<< /Type /Pages /Count " . count( $page_ids ) . " /Kids [ $kids_str ] >>" );
		$emit( $font1_id, $font1 );
		$emit( $font2_id, $font2 );
		foreach ( $this->pages as $i => $content ) {
			$pid = $page_ids[ $i ]; $cid = $content_ids[ $i ];
			$emit( $pid, "<< /Type /Page /Parent $pages_id 0 R /MediaBox [0 0 {$this->page_w} {$this->page_h}] /Resources << /Font << /F1 $font1_id 0 R /F2 $font2_id 0 R >> >> /Contents $cid 0 R >>" );
			$stream = $content;
			$emit( $cid, "<< /Length " . strlen( $stream ) . " >>\nstream\n$stream\nendstream" );
		}
		$xref_pos = strlen( $out );
		$max = $id;
		$out .= "xref\n0 $max\n0000000000 65535 f \n";
		for ( $i = 1; $i < $max; $i++ ) {
			$out .= sprintf( "%010d 00000 n \n", $offsets[ $i ] ?? 0 );
		}
		$out .= "trailer\n<< /Size $max /Root $catalog_id 0 R >>\nstartxref\n$xref_pos\n%%EOF";

		nocache_headers();
		header( 'Content-Type: application/pdf' );
		header( 'Content-Disposition: ' . ( $download ? 'attachment' : 'inline' ) . '; filename="' . $filename . '"' );
		header( 'Content-Length: ' . strlen( $out ) );
		echo $out; // phpcs:ignore
		exit;
	}
}
