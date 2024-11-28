<?php
// Includi il file di inizializzazione di Magento
use Magento\Framework\App\Bootstrap;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Magento\Catalog\Model\ProductFactory;
use Magento\Framework\App\ObjectManager;

require '/var/www/html/magento/app/bootstrap.php'; 
$bootstrap = Bootstrap::create(BP, $_SERVER);
$objectManager = $bootstrap->getObjectManager();
$state = $objectManager->get('Magento\Framework\App\State');
$state->setAreaCode('adminhtml'); // Set area di Magento

// carico la libreria PhpSpreadsheet
require '/var/www/html/magento/vendor/autoload.php'; 

$filePath = 'var/www/html/var/import/GSPR-ok.xlsx'; 

// Carica il file Excel
if (file_exists($filePath)) {
    // Carica il file Excel
    $spreadsheet = IOFactory::load($filePath);
    
    /* prendo ogni foglio del file  */
    $sheets = [
        'IT' => $spreadsheet->getSheetByName('IT'),
        'FR' => $spreadsheet->getSheetByName('FR'),
        'DE' => $spreadsheet->getSheetByName('DE')
    ];

    // scorriamo i fogli del file
    foreach ($sheets as $language => $sheet) {
        echo "Elaborando dati per la lingua: $language\n";
        
        // scorri le righe del foglio
        foreach ($sheet->getRowIterator() as $rowIndex => $row) {
            // salta la prima riga l'header
            if ($rowIndex === 1) {
                continue;
            }

            // ottieni lo SKU del prodotto 
            $sku = $sheet->getCell('B' . $rowIndex)->getValue();
            
            // prendo il prodotto da modificare 
            $product = $objectManager->create('Magento\Catalog\Model\Product')->loadByAttribute('sku', $sku);

            if ($product) {
               
                $conformitaLink = $sheet->getCell('G' . $rowIndex)->getValue(); // colonna G per conformità
                $istruzioniLink = $sheet->getCell('I' . $rowIndex)->getValue(); // colonna I per istruzioni

            // gestione dei valori vuoti: verifica se la cella è vuota
                   if (empty($conformitaLink)) {
                    $conformitaLink = null; 
                }

                if (empty($istruzioniLink)) {
                    $istruzioniLink = null;
                }

                // popolo gli attributi in Magento con i link
                switch ($language) {
                    case 'IT':
                        $product->setData('pdf_conformita', $conformitaLink);
                        $product->setData('pdf_istruzioniuso', $istruzioniLink);
                        break;
                    case 'FR':
                        $product->setData('pdf_conformita', $conformitaLink);
                        $product->setData('pdf_istruzioniuso', $istruzioniLink);
                        break;
                    case 'DE':
                        $product->setData('pdf_conformita', $conformitaLink);
                        $product->setData('pdf_istruzioniuso', $istruzioniLink);
                        break;
                }

                // Salva il prodotto (solo se è stato modificato)
                try {
                    $product->save();
                    echo "Prodotto con SKU $sku aggiornato correttamente per la lingua $language.\n";
                } catch (\Exception $e) {
                    echo "Errore durante il salvataggio del prodotto con SKU $sku: " . $e->getMessage() . "\n";
                }
            } else {
                echo "Prodotto con SKU $sku non trovato per la lingua $language.\n";
            }
        }
    }
} else {
    echo "File non trovato: $filePath\n";
}

echo "Processo completato.\n";
?>
