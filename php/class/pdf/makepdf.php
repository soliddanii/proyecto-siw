<?php

    require('fpdf.php');

    class MakePDF {
    
        public function createAndDownload($dataAnuncio){
        
            $data = $dataAnuncio[0];
            if($data[0] == null || empty($data[0])){
                return $dataAnuncio[1];
            }
        
            $pdf = new FPDF('P','pt','Letter');
            $pdf->SetTopMargin(40); 
            $pdf->SetLeftMargin(40); 
            $pdf->SetRightMargin(40); 
            $pdf->SetAutoPageBreak(false); 
            
            $pdf->AddPage(); 
            
            $pdf->SetFont('Arial', 'B', 20); 
            $pdf->SetFillColor(92, 134, 40); 
            $pdf->SetTextColor(225); 
            $pdf->Cell(0, 30, iconv('UTF-8', 'windows-1252', $data[0]['titulo']), 0, 1, 'C', true); 
            
            $pdf->SetFont('Arial', '', 12);
            $pdf->SetTextColor(0); 
            $pdf->SetY(100);
            if($data[0]['precio'] == '0.00'){
                $pdf->Cell(250, 13, 'Precio: GRATIS'); 
            }else{
                $pdf->Cell(250, 13, 'Precio: '.$data[0]['precio'].' €'); 
            }
            $pdf->Cell(100, 13, 'Teléfono de contacto: '.$data[0]['telefono']); 
            
            $pdf->SetY(120);
            $pdf->Cell(250, 13, 'Localizacion: '.iconv('UTF-8', 'windows-1252', $data[0]['localizacion'])); 
            $pdf->Cell(100, 13, 'Fecha de publicación: '.$data[0]['fecha']); 
            
            $pdf->SetY(140);
            $pdf->Cell(250, 13, 'Nick del vendedor: '.iconv('UTF-8', 'windows-1252', $data[0]['nickSeller'])); 
            $pdf->Cell(100, 13, 'Nombre del vendedor: '.iconv('UTF-8', 'windows-1252', $data[0]['nameSeller'])); 
            
            $pdf->SetY(180);
            $pdf->Cell(250, 13, 'Descripción:'); 
            $pdf->SetY(200);
            $pdf->MultiCell(0, 15, iconv('UTF-8', 'windows-1252', $data[0]['descripcion']));
            
            //Si hay imagenes
            if(!empty($data[1])){
                //$ds = DIRECTORY_SEPARATOR; '..'.$ds.'..'.$ds.
                $space_left = $pdf->GetPageHeight();-($pdf->GetY());
                if ($space_left < (380+25)) {
                    $pdf->AddPage(); // page break
                }else{
                    $pdf->SetY($pdf->GetY()+20);
                }
                $image = $data[1][0]['medium'];
                $pdf->Cell(380, 380, $pdf->Image($image, $pdf->GetX(), $pdf->GetY(), 380), 0, 0, 'L', false );
                
            }            
            
            $pdf->Output(); /* D: force download; F: Save to disk; S: return as string; I: inline*/
            //$pdf->Output('nombre.pdf','F');
            
            return $dataAnuncio[1];
        
        }
        
    }
    
?>