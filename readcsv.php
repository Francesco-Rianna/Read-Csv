<?php
// Nome del file
$filename = 'products.csv';
// Percorso da cui prelevare il file
$path = 'data/';
// File completo di percorso
$file = $path . $filename;
// Controllo se il file è leggibile
if ( ! is_readable( $file ) ) {
    die( 'Il file non è leggibile oppure non esiste!' );
}
// Leggo il contenuto del file
$rows = file( $file );
// Scorro l'array contenente le righe del file
foreach ( $rows as $row ) {
    // Separo le colonne
    $columns = explode( ',', $row );
    /* QUI IL CODICE CHE OCCORRE PER LAVORARE I DATI */
}
?>



<?php
// Nome del file
$filename = 'products.csv';
// Percorso del file
$path = 'data/';
// File completo di percorso
$file = $path . $filename;

// Controllo se il file è leggibile
if (!is_readable($file)) {
    die('Il file non è leggibile oppure non esiste!');
}

// Apro il file in lettura
if (($handle = fopen($file, 'r')) !== false) {
    // Leggo tutte le righe del CSV in un array
    $rows = [];
    while (($row = fgetcsv($handle, 1000, ',')) !== false) {
        $rows[] = $row; // Aggiungi ogni riga all'array
    }
    fclose($handle); // Chiudo il file

    // Ora puoi elaborare le righe con un ciclo foreach
    $headers = array_shift($rows); // Estrai la prima riga (intestazioni)
    foreach ($rows as $row) {
        $data = array_combine($headers, $row); // Associa le intestazioni ai valori
        $sku = $data['sku']; // Colonna "sku"
        $attributeValue = $data['attribute_value']; // Colonna "attribute_value"

        // Codice per elaborare i dati
        echo "Elaboro SKU: $sku con valore: $attributeValue\n";
    }
} else {
    echo "Errore nell'apertura del file.";
}
?>
<!-- con libreria -->
 <?php

use Magento\Framework\App\Bootstrap;

require 'app/bootstrap.php';
$bootstrap = Bootstrap::create(BP, $_SERVER);
$obj = $bootstrap->getObjectManager();

$state = $obj->get('Magento\Framework\App\State');
$state->setAreaCode('adminhtml');

$productRepository = $obj->get('Magento\Catalog\Api\ProductRepositoryInterface');
$csv = array_map('str_getcsv', file('path/to/file.csv'));

foreach ($csv as $row) {
    $sku = $row[0]; // SKU dal CSV
    $attributeValue = $row[1]; // Valore dell'attributo

    try {
        $product = $productRepository->get($sku);
        $product->setCustomAttribute('attribute_code', $attributeValue);
        $productRepository->save($product);
    } catch (\Exception $e) {
        echo "Errore con SKU {$sku}: " . $e->getMessage() . "\n";
    }
}
?>