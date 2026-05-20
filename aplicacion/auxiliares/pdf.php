<?php
require_once RUTA_BASE . '/scripts/TCPDF/tcpdf.php';

class pdf extends TCPDF
{
    // —————————————————————————————————————————————
    //            PROPIEDADES DINÁMICAS
    // —————————————————————————————————————————————
    
    private $titulo = "Informe de obras"; // → Título por defecto
    private $subtitulo = "~ DistarArt ~"; // → Subtítulo opcional
    private $infoFiltros = ""; // → Información de filtros aplicados
    
    // Colores del proyecto
    private $colorMorado = [108, 57, 216];     // → #6c39d8
    private $colorAzul = [75, 143, 231];      // → #4b8fe7
    private $colorGrisOscuro = [51, 51, 51];  // → #333333
    private $colorGrisClaro = [208, 208, 208]; // → #d0d0d0
    private $colorBlanco = [255, 255, 255]; // → #ffffff
    
    // —————————————————————————————————————————————
    //           MÉTODOS SETTERS
    // —————————————————————————————————————————————
    
    public function setTitulo($titulo)
    {
        $this->titulo = $titulo;
    }
    
    public function setSubtitulo($subtitulo)
    {
        $this->subtitulo = $subtitulo;
    }
    
    public function setInfoFiltros($info)
    {
        $this->infoFiltros = $info;
    }
    
    // —————————————————————————————————————————————
    //              ENCABEZADO
    // —————————————————————————————————————————————

    public function Header()
    {

        // Le añado una triple linea al encabezado para que quede más separado del borde superior
        $this->SetDrawColor($this->colorBlanco[0], $this->colorBlanco[1], $this->colorBlanco[2]);
        $this->SetLineWidth(1.5);
        $this->Line(10, $this->GetY(), 200, $this->GetY());
        $this->Ln(2);

        $this->SetDrawColor($this->colorBlanco[0], $this->colorBlanco[1], $this->colorBlanco[2]);
        $this->SetLineWidth(1.5);
        $this->Line(10, $this->GetY(), 200, $this->GetY());
        $this->Ln(2);

        $this->SetDrawColor($this->colorBlanco[0], $this->colorBlanco[1], $this->colorBlanco[2]);
        $this->SetLineWidth(1);
        $this->Line(10, $this->GetY(), 200, $this->GetY());
        $this->Ln(2);
        
        // Margenes del encabezado
        $this->SetMargins(10, 40, 10);
        //                ↓   ↓   ↓
        //        izquierda|arriba|derecha

        $this->SetDrawColor($this->colorBlanco[0], $this->colorBlanco[1], $this->colorBlanco[2]);
        $this->SetLineWidth(0.5);
        $this->Line(10, $this->GetY(), 200, $this->GetY());
        $this->Ln(2);

        // Fondo morado con título blanco
        $this->SetFillColor($this->colorMorado[0], $this->colorMorado[1], $this->colorMorado[2]);
        $this->SetTextColor(255, 255, 255); // → Texto blanco
        $this->SetFont('helvetica', 'B', 14);
        $this->Cell(0, 12, $this->titulo, 0, 1, 'C', true);
        
        // Si hay subtítulo lo mostramos debajo del título
        if ($this->subtitulo) {
            $this->SetTextColor($this->colorAzul[0], $this->colorAzul[1], $this->colorAzul[2]);
            $this->SetFont('helvetica', '', 10);
            $this->Cell(0, 6, $this->subtitulo, 0, 1, 'R');
        }
        
        // Línea separadora azul
        $this->SetDrawColor($this->colorAzul[0], $this->colorAzul[1], $this->colorAzul[2]);
        $this->SetLineWidth(0.5);
        $this->Line(10, $this->GetY(), 200, $this->GetY());
        $this->Ln(2);
        
        // Información de filtros
        if ($this->infoFiltros) {
            $this->SetTextColor($this->colorGrisOscuro[0], $this->colorGrisOscuro[1], $this->colorGrisOscuro[2]);
            $this->SetFont('helvetica', 'I', 8);
            $this->Cell(0, 5, $this->infoFiltros, 0, 1, 'L');
        }
        
        // Fecha de generación a la derecha
        $this->SetTextColor($this->colorGrisOscuro[0], $this->colorGrisOscuro[1], $this->colorGrisOscuro[2]);
        $this->SetFont('helvetica', '', 8);
        $this->Cell(0, 5, 'Generado: ' . date('d/m/Y H:i:s'), 0, 1, 'R');
        
        // Espacio después del encabezado
        $this->Ln(2);
    }

    // —————————————————————————————————————————————
    //               PIE DE PÁGINA
    // —————————————————————————————————————————————

    public function Footer()
    {
        $this->SetY(-12);
        $this->SetFont('helvetica', '', 8);
        $this->SetTextColor($this->colorGrisClaro[0], $this->colorGrisClaro[1], $this->colorGrisClaro[2]);
        
        // Línea separadora
        $this->SetDrawColor($this->colorGrisClaro[0], $this->colorGrisClaro[1], $this->colorGrisClaro[2]);
        $this->SetLineWidth(0.3);
        $this->Line(10, $this->GetY(), 200, $this->GetY());
        $this->Ln(1);
        
        // Página
        $this->SetTextColor($this->colorAzul[0], $this->colorAzul[1], $this->colorAzul[2]);
        $this->Cell(0, 5, 'DistarArt | Página ' . $this->getAliasNumPage() . ' de ' . $this->getAliasNbPages(), 0, 0, 'C');
    }
}