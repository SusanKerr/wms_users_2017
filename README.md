# wms_users_2017

Step 2. 

$ cd /your-bitnami-installation-directory/apache2/htdocs
/c/wamp/bin/apache/apache2.4.23/htdocs

$ git clone https://github.com/SusanKerr/wms_users_2017.git

  Star 0  Fork 0 OCLC-Developer-Network/wms_users_2017
 Code  Issues 0  Pull requests 0  Projects 0  Wiki Insights 
Branch: master Find file Copy pathwms_users_2017/tutorial/tutorial-05.md
659cc76  3 hours ago
 Coombs,Karen Fix service_url creation in tutorial
1 contributor
RawBlameHistory     
666 lines (626 sloc)  21.1 KB
A Beginner's Guide to Working with WorldShare APIs

OCLC WMS Global Community + User Group Meeting 2017: Pre-Conference Workshop

Tutorial Part 5 - Creating Models Using Test Driven Development

Test Setup

Create a file called phpunit.xml
Open phpunit.xml
Configure how phpunit should run
set directory where tests live
specify logging of tests
specify what files are being tested
add listener for PHP_VCR to handle mocks
<?xml version="1.0" encoding="UTF-8"?>
<phpunit bootstrap="tests/bootstrap.php"
         colors="true"
         processIsolation="false"
         stopOnFailure="false"
         syntaxCheck="false"
         convertErrorsToExceptions="true"
         convertNoticesToExceptions="true"
         convertWarningsToExceptions="true"
         testSuiteLoaderClass="PHPUnit_Runner_StandardTestSuiteLoader">     
    <testsuites>
    <testsuite>
      <directory>tests</directory>
    </testsuite>
  </testsuites>
  <logging>
    <log type="coverage-clover" target="clover.xml"/>
    <log type="coverage-html" target="tests/codeCoverage" charset="UTF-8"/>
  </logging>
  <filter>
    <whitelist>
        <directory suffix=".php">app/model</directory>
    </whitelist> 
  </filter>  
  <listeners>
      <listener class="PHPUnit_Util_Log_VCR" file="vendor/php-vcr/phpunit-testlistener-vcr/PHPUnit/Util/Log/VCR.php" />
    </listeners>
  </phpunit>
Populate the tests directory
Add a file called getMocks.php to the tests directory
Add this code to the file and save it.
Add a file called mockBuilder.yml to the tests directory
Add this code to the file and save it.
Create a directory within tests called mocks
Add the following files to mocks containing the linked code
bibSuccess
bibSuccessJournal
failureExpiredAccessToken
failureInvalidAccessToken
marcRecord.xml
In tests directory create a file named bootstrap.php
Require vendor autoload file
    require_once __DIR__ . '/../vendor/autoload.php';
Setup HTTP mocking
    \VCR\VCR::configure()->setCassettePath(__DIR__ . '/mocks');
    \VCR\VCR::configure()->enableRequestMatchers(array('method', 'url', 'host'));
Write your first test

In tests directory create a file named BibTest.php to test your Bib Class
Open BibTest.php and add use statements for class you want to use (WSKey and Access Token)
    use OCLC\Auth\WSKey;
    use OCLC\Auth\AccessToken;
define the BibTest Class as extending PHPUnit_Framework_TestCase
    class BibTest extends \PHPUnit_Framework_TestCase
Create a setup function in the BibTest Class. This runs before every test case.
Create mock Access Token object that returns a specific value
    function setUp()
    {   
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
Write for Test creating a Bib
Create a new Bib object
Test that it is an instance of a Bib object
Pass bib variable to next test
    function testCreateBib(){
        $bib = new Bib();
        $this->assertInstanceOf('Bib', $bib);
        return $bib;
    }
Make the test pass by creating Bib class and constructor
In the app/model directory create a file named Bib.php to represent the Bib Class
Open Bib.php, declare Bib class and add use statements for class you want to use (WSKey and Access Token)
    use GuzzleHttp\Client, GuzzleHttp\Exception\RequestException, GuzzleHttp\Psr7\Response;
Create a constructor for the Bib class
    function __construct() {
    }
Run tests
vendor/bin/phpunit
Setting a Record

Make sure testCreateBib passes
Load a MARCXML file into the existing bib object.
Get the first record in the collection and set this to be the "record" attribute in the bib object.
Test record attribute of the bib object is a "File_MARC_Record".
Pass thh bib variable to the next test.
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
Create a setRecord function in the Bib Class
Add variable for record
protected $record;
Make sure the $record is a File_MARC_record object. Otherwise throw an Exception
Set record attribute equal to record value
    public function setRecord($record){
        if (!is_a($record, 'File_MARC_Record')) {
            Throw new \BadMethodCallException('You must pass a valid File_MARC_Record');
        }
        $this->record = $record;
    }
    
Run tests
vendor/bin/phpunit
Get Record

Write a test for getting a record
Make sure testSetRecord passes
Test that it is an instance of a File_MARC_record object
Pass bib variable to next test
    /**
     * Get Record
     * @depends testSetRecord
     */
    function testGetRecord($bib){
        $this->assertInstanceOf("File_MARC_Record", $bib->getRecord());
        return $bib;
    }
Write function to get a record in Bib class
    public function getRecord()
    {
        return $this->record;
    }
Run tests
vendor/bin/phpunit
Get Id

Write a test for getting a record
Make sure testGetRecord passes
Test getId method returns appropriate value.
    /**
     * Get Id
     * @depends testGetRecord
     */
    function testGetId($bib) {
        $this->assertEquals("70775700", $bib->getId());
    }
Create code for handling Id

Add variable for id
protected $id;
Write function to get the Id in Bib class
    public function getId()
    {
        if (empty($this->id)){
            if (is_numeric($this->record->getField('001')->getData())) {
                $this->id = $this->record->getField('001')->getData();
            } else {
             $this->id = substr($this->record->getField('001')->getData(), 3);
            }
            
        }
        return $this->id;
    }
Run tests

vendor/bin/phpunit
Get OCLCNumber

Write a test for getting a record
Make sure testGetRecord passes
Test that getOCLCNumber method returns the appropriate value
    /**
     * Get OCLCNumber
     * @depends testGetRecord
     */
    function testGetOCLCNumber($bib) {
        $this->assertEquals("ocm70775700", $bib->getOCLCNumber());
    }
    
Write function to get a record in Bib class
Use built-in function on File_MARC_Record object to get the data in the 001
    public function getOCLCNumber()
    {
        return $this->record->getField('001')->getData();
    }
Run tests
vendor/bin/phpunit
Get Title

Write a test for getting a record
Make sure testGetRecord passes
Test that getTitle method returns appropriate value
    /**
     * Get Title
     * @depends testGetRecord
     */
    function testGetTitle($bib) {
        $this->assertEquals("Dogs and cats", $bib->getTitle());
    }
Write function to get a record in Bib class
Use built-in functions on File_MARC_Record object to get the data
    public function getTitle()
    {
        $title = $this->record->getField('24.', true)->getSubfield('a')->getData();
        if ($this->record->getField('24.', true)->getSubfield('b')){
            $title .= $this->record->getField('24.', true)->getSubfield('b')->getData();
        }
        $title = trim($title, " /");
        return $title;
    }
Run tests
vendor/bin/phpunit
Get Author

Write a test for getting a record
Make sure testGetRecord passes
Test that getAuthor method returns appropriate value
    /**
     * Get Author
     * @depends testGetRecord
     */
    function testGetAuthor($bib) {
        $this->assertEquals("Jenkins, Steve", $bib->getAuthor());
    }
Write function to get a record in Bib class
Use built-in functions on File_MARC_Record object to get the data
    public function getAuthor()
    {
        if ($this->record->getField('100', true)) {
            $author = $this->record->getField('100', true)->getSubfield('a')->getData();
        } elseif ($this->record->getField('110', true)) {
            $author = $this->record->getField('110', true)->getSubfield('a')->getData();
        } elseif ($this->record->getField('111', true)){
            $author = $this->record->getField('111', true)->getSubfield('a')->getData();
        } elseif ($this->record->getField('700', true)) {
            $author = $this->record->getField('700', true)->getSubfield('a')->getData();
        } elseif ($this->record->getField('710', true)) {
            $author = $this->record->getField('710', true)->getSubfield('a')->getData();
        } elseif ($this->record->getField('711', true)) {
            $author = $this->record->getField('711', true)->getSubfield('a')->getData();
        } else {
            $author = "";
        }
        $author = rtrim($author, ',');
        if (strpos($author, ".") > 0) {
            $author= rtrim($author, '.');
        }
        return $author;
    }
Run tests
vendor/bin/phpunit
Getting a Bib from the API

Tell tests what file to use for mocks
Find a Bib
Test that object returned is an instance of a Bib
Pass bib variable to next test
    /**
     *@vcr bibSuccess
     */
    function testGetBib(){
        $bib = Bib::find(70775700, $this->mockAccessToken);
        $this->assertInstanceOf('Bib', $bib);
        return $bib;
    }
Make test pass by creating a static "find" function for the Bib
Create a variable to hold the service url
    public static $serviceUrl = 'https://worldcat.org/bib/data/';
Create a variable to hold the user agent
    public static $userAgent = 'OCLC DevConnect demo';
Make sure a a valid OCLC Number is passed in
Make sure a valid Access Token is passed in
Create a url for the request
Create an HTTP client
Create an array of headers
try to make the HTTP request
If successful
Parse the response body XML
Pull out MARC record string and use it to create a File_MARC_Record
If fails
Pass response off to BibError::parseError to handle
    public static function find($oclcnumber, $accessToken){
        if (!is_numeric($oclcnumber)){
            Throw new \BadMethodCallException('You must pass a valid OCLC Number');
        } elseif (!is_a($accessToken, '\OCLC\Auth\AccessToken')) {
            Throw new \BadMethodCallException('You must pass a valid Access Token');
        }
        $url = static::$serviceUrl . $oclcnumber . '?classificationScheme=LibraryOfCongress&holdingLibraryCode=MAIN';
        $client = new Client();
        $headers = array();
        $headers['Authorization'] = 'Bearer ' . $accessToken->getValue();
        $headers['Accept'] = 'application/atom+xml;content="application/vnd.oclc.marc21+xml"';
        
        try {
            $response = $client->request('GET', $url, ['headers' => $headers]);
            $response_body = simplexml_load_string($response->getBody());
            
            //We parse the MARCXML out of the Atom Response
            $response_body->registerXPathNamespace('atom', 'http://www.w3.org/2005/Atom');
            $response_body->registerXPathNamespace('rb', 'http://worldcat.org/rb');
            $marc_xml = $response_body->xpath('//atom:content/rb:response/child::*');
            //We want a File_MARC Record created from the MARC
            $records = new File_MARCXML($marc_xml[0]->asXML(), File_MARC::SOURCE_STRING);
            $bib = new Bib();
            $bib->id = $oclcnumber;
            $bib->record = $records->next();
            return $bib;
        } catch (RequestException $error) {
            $bibError = new BibError();
            $bibError->setRequestError($error->getResponse());
            return $bibError;
        }
    }
Run tests
vendor/bin/phpunit
Write test for getting MarcRecord object

Make sure testGetBib passes
Test that getRecord method on bib object returns a File_MARC_Record
Pass bib variable to next test
    /**
     * can parse Single Bib string
     * @depends testGetBib
     */
    function testParseMarc($bib)
    {
        $this->assertInstanceOf("File_MARC_Record", $bib->getRecord());
        return $bib;
    }
Run tests
vendor/bin/phpunit
Write test for getting values from Bib

Make sure testParseMarc passes
Test that getID method on bib object returns a value of 70775700
Test that getOCLCNumber method on bib object returns a value of ocm70775700
Test that getTitle method on bib object returns a value of Dogs and cats
Test that getAuthor method on bib object returns a value of Jenkins, Steve
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
Run tests
vendor/bin/phpunit
Write the first test for the BibError Class

In tests directory create a file named BibErrorTest.php to test your BibError Class
Open BibErrorTest.php and add use statements for class you want to use (WSKey and Access Token)
    use OCLC\Auth\WSKey;
    use OCLC\Auth\AccessToken;
    use GuzzleHttp\Psr7\Response;
define the BibErrorTest Class as extending PHPUnit_Framework_TestCase
    class BibErrorTest extends \PHPUnit_Framework_TestCase
Create a setup function in the BibErrorTest Class. This runs before every test case.
Create mock Access Token object that returns a specific value
    function setUp()
    {   
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
Write for Test creating a BibError
Create a new BibError object
Test that it is an instance of a BibError object
    function testCreateBibError(){
        $error = new BibError();
        $this->assertInstanceOf('BibError', $error);
        return $error;
    }
Make the test pass by creating BibError class and constructor
In the app/model directory create a file named BibError.php to represent the BibError Class
Open BibError.php and declare BibError class
Create a constructor for the BibError class
    function __construct() {
    }
Run tests
vendor/bin/phpunit
Setting a Request Error

Create Test for setting a request error
    /**
     * Set Request Error
     * @depends testCreateBibError
     */
    function testSetRequestError($error){
        $request_error = new Response(401, [], '<?xml version="1.0" encoding="UTF-8" standalone="yes"?><error><code type="http">401</code><message>AccessToken {tk_12345} is invalid</message><detail>Authorization header: Bearer tk_12345</detail></error>');
        $error->setRequestError($request_error);
        $this->assertAttributeInstanceOf("GuzzleHttp\Psr7\Response", 'requestError', $error);
        return $error;
    }
Write code for setting a request error
    function setRequestError($error)
    {
        if (!is_a($error, 'GuzzleHttp\Psr7\Response')) {
            Throw new \BadMethodCallException('You must pass a valid Guzzle Http PSR7 Response');
        }
        $this->requestError = $error;
        if (implode($this->requestError->getHeader('Content-Type')) !== 'text/html;charset=utf-8'){
            $error_response = simplexml_load_string($this->requestError->getBody());
            $this->code = (integer) $error_response->code;
            $this->message = (string) $error_response->message;
            $this->detail = (string) $error_response->detail;
        } else {
            $errorObject->code = (integer) $this->requestError->getStatusCode();
        }
        
    }
Getting a Request Error

Create Test for getting a request error
    /**
     * Get Request Error
     * @depends testSetRequestError
     */
    function testGetRequestError($error){
        $this->assertInstanceOf("GuzzleHttp\Psr7\Response", $error->getRequestError());
        return $error;
    }
Add variable for request error
protected $requestError;
Write code for getting a request error
    function getRequestError()
    {
        return $this->requestError;
    }
Get Code

Create Test for getting error code
    /**
     * Get Code
     * @depends testGetRequestError
     */
    function testGetCode($error) {
        $this->assertEquals('401', $error->getCode());
    }
Write code to make getCode test pass
Add variable for code
protected $code;
Create function to retrieve error code
    function getCode() {
        return $this->code;
    }
Get Message

Create a test for getting the error message
    /**
     * Get Message
     * @depends testGetRequestError
     */
    function testGetMessage($error) {
        $this->assertEquals('AccessToken {tk_12345} is invalid', $error->getMessage());
    }
Write code to make getMessage test pass
Add variable for message
protected $message;
Create function to retrieve error message
    function getMessage() {
        return $this->message;
    }
Get Detail

Create a test for getting the error detail
    /**
     * Get Detail
     * @depends testGetRequestError
     */
    function testGetDetail($error) {
        $this->assertEquals('Authorization header: Bearer tk_12345', $error->getDetail());
    }
Write code to make getDetail test pass
Add variable for detail
protected $detail;
Create function to retrieve error detail
    function getDetail() {
        return $this->detail;
    }
Test that an API error can be properly parsed

Create test for parsing API error
Tell tests what file to use for mocks
Call Bib::find in a failed fashion
Test $error is an instance of BibError
Test the getCode() method returns 401
Test the getMessage() method returns AccessToken {tk_12345} is invalid
Test the getDetail() method returns Authorization header: Bearer tk_12345
    /**
     * @vcr failureInvalidAccessToken
     * Invalid Access Token
     */
    function testErrorInvalidAccessToken(){
        $error = Bib::find(70775700, $this->mockAccessToken);
        $this->assertInstanceOf('BibError', $error);
        $this->assertEquals('401', $error->getCode());
        $this->assertEquals('AccessToken {tk_12345} is invalid', $error->getMessage());
        $this->assertEquals('Authorization header: Bearer tk_12345', $error->getDetail());
    }
on to Part 6

back to Part 4
© 2017 GitHub, Inc.

