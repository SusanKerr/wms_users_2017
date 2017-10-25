<?php

use OCLC\Auth\WSKey;
use OCLC\Auth\AccessToken;

class BibTest extends \PHPUnit_Framework_TestCase {
    function setUp() {   
        $options = array(
                'authenticatingInstitutionId' => 128807,
                'contextInstitutionId' => 128807,
                'scope' => array('WorldCatMetadataAPI')
        );
        $this->mockAccessToken = $this->getMockBuilder(AccessToken::class)
        ->setConstructorArgs(array('client_credentials', $options))
        ->getMock();
        
        $this->mockAccessToken->expects($this->any())
        ->method('getValue')
        ->will($this->returnValue('tk_12345'));
	}

	function testCreateBib(){
        $bib = new Bib();
        $this->assertInstanceOf('Bib', $bib);
        return $bib;
    }
/**
     * Set File_MARC_Record
     * @depends testCreateBib
     */
    function testSetRecord($bib){
        $records = new File_MARCXML(file_get_contents(__DIR__ . '/mocks/marcRecord.xml'), File_MARC::SOURCE_STRING);
        $record = $records->next();
        $bib->setRecord($record);
        $this->assertAttributeInstanceOf("File_MARC_Record", 'record', $bib);
        return $bib;
    }

	/**
     * Get Record
     * @depends testSetRecord
     */
    function testGetRecord($bib){
        $this->assertInstanceOf("File_MARC_Record", $bib->getRecord());
        return $bib;
    }
	
 /**
     * Get Id
     * @depends testGetRecord
     */
    function testGetId($bib) {
        $this->assertEquals("70775700", $bib->getId());
    }

	/**
     * Get OCLCNumber
     * @depends testGetRecord
     */
    function testGetOCLCNumber($bib) {
        $this->assertEquals("ocm70775700", $bib->getOCLCNumber());
    }
	
    /**
     * Get Title
     * @depends testGetRecord
     */
    function testGetTitle($bib) {
        $this->assertEquals("Dogs and cats", $bib->getTitle());
    }
	
	/**
     * Get Author
     * @depends testGetRecord
     */
    function testGetAuthor($bib) {
        $this->assertEquals("Jenkins, Steve", $bib->getAuthor());
    }

	/**
     *@vcr bibSuccess
     */
    function testGetBib(){
        $bib = Bib::find(70775700, $this->mockAccessToken);
        $this->assertInstanceOf('Bib', $bib);
        return $bib;
    }
	
    /**
     * can parse Single Bib string
     * @depends testGetBib
     */
    function testParseMarc($bib)
    {
        $this->assertInstanceOf("File_MARC_Record", $bib->getRecord());
        return $bib;
    }
	
    /**
     * can parse Single Copy string
     * @depends testParseMarc
     */
    function testParseLiterals($bib)
    {
        $this->assertEquals("70775700", $bib->getId());
        $this->assertEquals("ocm70775700", $bib->getOCLCNumber());
        $this->assertEquals("Dogs and cats", $bib->getTitle());
        $this->assertEquals("Jenkins, Steve", $bib->getAuthor());
    }
}