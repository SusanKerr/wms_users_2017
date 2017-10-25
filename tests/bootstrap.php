<?php
require_once __DIR__ . '/../vendor/autoload.php';
\VCR\VCR::configure()->setCassettePath(__DIR__ . '/mocks');
\VCR\VCR::configure()->enableRequestMatchers(array('method', 'url', 'host'));
