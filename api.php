<?php
include "./dev/pprint.php";
require "./SleekDB/Store.php";
// phpinfo();
// error_reporting(E_ALL & ~E_NOTICE & ~E_DEPRECATED & ~E_WARNING & ~E_STRICT);
// ini_set("display_errors", 0);
// ini_set('error_reporting', E_ALL);
// ini_set('display_startup_errors', 'On');
use SleekDB\Store;
use SleekDB\Query;




//
// JSON OUTPUT
//
function printJSON($json){
  header("Access-Control-Allow-Origin: *");
  header("Content-Type: application/json; charset=UTF-8");
  header("Access-Control-Allow-Methods: OPTIONS,GET,POST,PUT,DELETE");
  header("Access-Control-Max-Age: 3600");
  header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");
  echo json_encode($json);
}




// DEBUG POST
if (isset($_POST)){
  // printJSON($_POST);
  // pprint($_POST);
} 

// DEBUG GET
if (isset($_GET)){ 
    // pprint($_GET);
  // printJSON($_GET);
}



// mod rewrite in .htaccess
// https://dev.rasal.de/Cotes/search=%22function()%20%7B]%22in%22css,js%22
// if(0 === strpos($_GET['query'],'search')){
//   $parts = explode('"',$_GET['query']);
//   $needle = $parts[1];
//   $haystack = $parts[3];
//   echo "search for '<span style='color:darkred'>".$needle."</span>' in '<span style='color:darkred'>".$haystack."</span>'";
// }

// exit();


/////////////////////////////////////

 


//////////////////////////////////////////////
// get and split uri with PHP
$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$urlParts = explode('/',$uri);
if ((isset($urlParts[2]) && $urlParts[2] != 'api')) {
  // header("HTTP/1.1 404 Not Found");
  // echo "no API call";
  // exit();
}
foreach ($urlParts as $key => $value) {

  // https://dev.rasal.de/Codes/api/search=vue,php
  if(0 === strpos($value,'search=')){
    $value = str_replace('search=','',$value);
    $items = explode(',', $value);
    $case = 'search';
  }
  // https://dev.rasal.de/Codes/api/ID/33
  if(0 === strpos($value,'ID')){
    $value = str_replace('ID','',$value);
    $ID = $urlParts[$key+1];
    $case = 'ID';
  }
}
// pprint($ID);
// pprint($items);
// pprint($urlParts);




$conf=[];

//
// STORE CONFIG
// 
$conf['storeConf'] = [
  "auto_cache" => true,
  "cache_lifetime" => null,
  // "timeout" => 120,
  "primary_key" => "_id",
  "search" => [
    "min_length" => 2,
    "mode" => "or",
    "score_key" => "scoreKey",
    "algorithm" => Query::SEARCH_ALGORITHM["hits"]
  ]
];


//
// CREATE NEW STORE
$conf['DBdir'] = "/db";
$conf['Username'] = "kolja2";
$conf['ItemStore'] = new Store($conf['Username'], __DIR__.$conf['DBdir'], $conf['storeConf']); 

//
// creating a new store object
//
if(isset($_GET['create']) && isset($_POST)){
  $newItems = $_POST;
  // TEST
  $rDates = rDate();
  $newItems['date'] = $rDates['cre'];
  $newItems['date_mod'] = $rDates['mod'];
  $lang = rLang();
  $newItems['lang'] = $lang;
  $newItems['code'] = rCode($lang);
  $newItems['tags'] = rTags();
  $newItems['desc'] = file_get_contents('https://loripsum.net/generate.php?p=1');
  // insert items in db
  $items = $conf['ItemStore']->insert($newItems);
  // header("Content-Type: application/json");
  // echo json_encode($items);
  // print_r($items);
} 





//
// all Items
//
// if(isset($_GET['all'])){
//   $result = $conf['ItemStore']->findAll();
//   pprint($result);
//   exit;
// }


 


//
// search Items
//
// if(isset($_GET['search'])){
//   $searchOptions = [
//     "minLength" => 2,
//     "mode" => "or",
//     "scoreKey" => "scoreKey",
//     "algorithm" => Query::SEARCH_ALGORITHM["hits"]
//   ];
//   $result =  $conf['ItemStore']->createQueryBuilder()
//   ->search(["lang","desc"], "PHP", $searchOptions)
//   ->getQuery()
//   ->fetch();

//   // pprint($result);
//   printJSON($result);
//   exit;
// }


// DEBUG GET
// if (isset($_GET['search'])){ 
//   search('Lorem','lang, desc');
// }

$data = json_decode(file_get_contents('php://input'), true);
if($data['search']){
  $needle = trim($data['search']['needle'],'"');
  $haystack = trim($data['search']['haystack'],'"');
  // printJSON($data);
  search($needle,$haystack);
}

 

function search($needle,$haystack){ 
  global $conf;
  $haystack = explode(',', $haystack);
  $searchOptions = [
    "minLength" => 2,
    "mode" => "or",
    "scoreKey" => "scoreKey",
    "algorithm" => Query::SEARCH_ALGORITHM["hits"]
  ];
  $result =  $conf['ItemStore']->createQueryBuilder()
  ->search($haystack, $needle, $searchOptions)
  ->getQuery()
  ->fetch();

  // pprint($result);
  printJSON($result);
  exit;
}





















//
// RANDOM FUNCTIONS
//
function rDate(){
  $min = strtotime('10.02.1982');
  $max = strtotime('01.01.2000');
  $val = rand($min, $max);
  $date['cre'] = date('Y-m-d H:i:s', $val);
  $val = rand($val , $max);
  $date['mod'] = date('Y-m-d H:i:s', $val);
  return $date;
}


function rLang(){
  $lang = array("JS", "CSS", "HTML", "PHP", "Vue", "VB", "Python","JSON", "Basic", "Bash");
  $rand_key = array_rand($lang, 1);
  return $lang[$rand_key];

}

function rTags(){
  $lang = array("JS", "CSS", "HTML", "PHP", "Vue", "VB", "Python","JSON");
  $rand_keys = array_rand($lang, rand(1,3));
  if(!is_array($rand_keys)){
    $rand_keys = [1];
  }
  $result = "";
  for ($i=0; $i < count($rand_keys); $i++){
    $result .= $lang[$rand_keys[$i]] . ", ";
  }  
  return substr($result, 0, -2);
}

function rCode($lang){
    switch ($lang) {
      case 'CSS':
        return "
        .burger {
          display: flex;
          flex-direction: column;
          justify-content: space-between;
          width: 30px;
          height: 20px;
          cursor: pointer;
        }
        
        .burger span {
          height: 1px;
          display: block;
          background: #000;
        }
        ";
        break;
      
        case 'Python':
        return "
        # Program make a simple calculator
        # This function adds two numbers
        def add(x, y):
            return x + y
        # This function subtracts two numbers
        def subtract(x, y):
            return x - y
        # This function multiplies two numbers
        def multiply(x, y):
            return x * y
        # This function divides two numbers
        def divide(x, y):
            return x / y
        print(\"Select operation.\")
        print(\"1.Add\")
        print(\"2.Subtract\")
        print(\"3.Multiply\")
        print(\"4.Divide\")
        while True:
            # take input from the user
            choice = input(\"Enter choice(1/2/3/4): \")
      ";
      break;
      case 'PHP':
        return "
        $colors = array(\"red\", \"green\", \"blue\", \"yellow\"); 

        foreach ($colors as $value) {
          echo \"$value <br>\";
        }
        ";
        break;
      case 'VB':
        return "
        Sub Main()
        On Error GoTo Failed
          Dim app As Netica.Application
          app = New Netica.Application
          app.Visible = True
          Dim net_file_name As String
          net_file_name = System.AppDomain.CurrentDomain.BaseDirectory() & \"..\..\..\ChestClinic.dne\"
          Dim net As Netica.Bnet
          net = app.ReadBNet(app.NewStream(net_file_name))
          net.Compile()
          Exit Sub
        Failed:
          MsgBox(\"NeticaDemo: Error \" & (Err.Number And &H7FFFS) & \": \" & Err.Description)
        End Sub
        ";
        break;
      case 'HTML':
        return "
        <table style=\"width:100%\">
          <tr>
            <th>Firstname</th>
            <th>Lastname</th> 
            <th>Age</th>
          </tr>
          <tr>
            <td>Jill</td>
            <td>Smith</td>
            <td>50</td>
          </tr>
          <tr>
            <td>Eve</td>
            <td>Jackson</td>
            <td>94</td>
          </tr>
          <tr>
            <td>John</td>
            <td>Doe</td>
            <td>80</td>
          </tr>
        </table>
        ";
        break;
        case 'Vue':
          return "
            <div id=\"app-4\">
            <ol>
              <li v-for=\"todo in todos\">
                {{ todo.text }}
              </li>
            </ol>
          </div>
          var app4 = new Vue({
            el: '#app-4',
            data: {
              todos: [
                { text: 'Learn JavaScript' },
                { text: 'Learn Vue' },
                { text: 'Build something awesome' }
              ]
            }
          })
          ";
          break;
          case 'JS':
            return "
            class Rectangle {
              constructor(height, width) {
                this.height = height
                this.width = width
              }
            
              get area() {
                return this.calcArea()
              }
            
              calcArea() {
                return this.height * this.width
              }
            }
            
            const square = new Rectangle(10, 10)
            
            console.log(square.area) // 100
            ";
            break;
          case 'JSON':
            return "
            {
              // Steuert den Buchstabenabstand für das Terminal. Es handelt sich um einen ganzzahligen Wert, der die Menge zusätzlicher Pixel repräsentiert, die zwischen Zeichen hinzugefügt werden sollen.
              \"terminal.integrated.letterSpacing\": 0,

              // Steuert die Zeilenhöhe für das Terminal. Diese Zahl wird mit dem Schriftgrad für das Terminal multipliziert, um die tatsächliche Zeilenhöhe in Pixeln zu erhalten.
              \"terminal.integrated.lineHeight\": 1,

              // Experimentell: Lokales Echo wird deaktiviert, wenn mindestens einer dieser Programmnamen im Terminaltitel gefunden wird.
              \"terminal.integrated.localEchoExcludePrograms\": [
                \"vim\",
                \"vi\",
                \"nano\",
                \"tmux\"
              ],
              // Experimentell: Länge der Netzwerkverzögerung in Millisekunden, mit der lokale Bearbeitungen auf dem Terminal ausgegeben werden, ohne auf Serverbestätigung zu warten.  
              \"terminal.integrated.localEchoLatencyThreshold\": 30,

              // Experimentell: Endstil von lokal ausgegebenem Text, entweder ein Schriftschnitt oder eine RGB-Farbe.
              \"terminal.integrated.localEchoStyle\": \"dim\",

              // Steuert, ob eine Auswahl erzwungen werden soll, wenn unter macOS die Tastenkombination WAHLTASTE+Klick verwendet wird. Hiermit wird eine reguläre (Zeilen-) Auswahl erzwungen und die Verwendung des Modus zur Spaltenauswahl unterbunden. Dies ermöglicht das Kopieren und Einfügen über die reguläre Terminalauswahl, wenn beispielsweise der Mausmodus in tmux aktiviert ist.
              \"terminal.integrated.macOptionClickForcesSelection\": false,

              // Steuert, ob die WAHLTASTE im Terminal unter macOS als Meta-Taste betrachtet wird.
              \"terminal.integrated.macOptionIsMeta\": false,
            }
            ";
            break; 
      default:
        return "
        empyt
        ";
        break;
    }
}

?>