<?php

use atoum;

$report = $script->addDefaultReport();

// LOGO
// This will add a green or red logo after each run depending on its status.
$report->addField(new atoum\report\fields\runner\result\logo());

// CODE COVERAGE SETUP
$coverageField = new atoum\report\fields\runner\coverage\html(
    basename(__DIR__),
    $coveragePath = __DIR__ . '/tests/_coverage'
);

$coverageField->setRootUrl('file://' . $coveragePath . '/');

if (!file_exists($coveragePath)) {
    mkdir($coveragePath);
}

$report->addField($coverageField);

$runner->setBootstrapFile(__DIR__ . '/.bootstrap.atoum.php');
$runner->addTestsFromDirectory(__DIR__ . '/tests');

$script->noCodeCoverageForNamespaces('Symfony');