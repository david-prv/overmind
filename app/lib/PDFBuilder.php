<?php

/**
 * Class PDFBuilder
 *
 * <p>
 * This class is an extension of the pdf class
 * from {@see http://fpdf.de/}. It is used to auto-generate the
 * risk analysis result as PDF, using the scan reports of the
 * tools of the bundle.
 * </p>
 *
 * @author David Dewes <hello@david-dewes.de>
 */
class PDFBuilder extends FPDF
{

    private ?string $targetUrl = NULL;
    private ?array $toolsUsed = NULL;

    ////////////////////////
    // OVERRIDDEN METHODS //
    ////////////////////////

    /**
     * PDFBuilder constructor.
     *
     * @param string $orientation
     * @param string $unit
     * @param string $size
     */
    function __construct($orientation = 'P', $unit = 'mm', $size = 'A4')
    {
        parent::__construct($orientation, $unit, $size);
    }

    /**
     * Build the PDF Header (automatically used)
     */
    function Header()
    {
        $this->SetFont('Arial', 'B', 16);
        $this->Cell(70);
        $this->Cell(30, 0, 'Risk Assessment', 0, 'C');
        $this->Image('https://etage-4.de/etage4_wp/wp-content/uploads/2020/05/Logo_3000_dark.png',10,6,30);
        $this->Ln(10);
        $this->SetFont('Arial', '', 10);
        $this->SetX(9);
        $this->Cell(0, 0, "Target: $this->targetUrl");
        $this->Ln(5);
        $this->SetX(9);
        $this->Cell(0, 0, date('d/m/Y'), 0, 0);
        $this->Ln(20);
    }

    /**
     * Build the PDF Footer (automatically used)
     */
    function Footer()
    {
        $this->AliasNbPages();
        $this->SetY(-15);
        $this->SetFont('Arial', 'I', 8);
        $this->Cell(0, 10, 'Page ' . $this->PageNo() . '/{nb}', 0, 0, 'C');
    }

    /////////////////
    // OWN METHODS //
    /////////////////

    /**
     * Defines the project name
     *
     * @param string $url
     */
    public function setTargetUrl(string $url): void
    {
        $this->targetUrl = $url;
    }

    /**
     * Defines which tools were used,
     * thus, which reports we need to use
     *
     * @param array $tools
     */
    public function setToolsUsed(array $tools): void
    {
        $this->toolsUsed = $tools;
    }

    /**
     * Dummy debug method
     */
    public function dummy()
    {
        $this->AddPage();
        $this->Ln(30);
        $this->Image('http://chart.googleapis.com/chart?cht=p3&chd=t:60,40&chs=250x100&chl=Hello|World', 60, 30, 90, 0, 'PNG');
    }

    /**
     * Streams PDF file data
     */
    public function stream(): void
    {
        if ($this->targetUrl === NULL) die("Something went wrong! Please try again");
        $this->Output();
    }
}