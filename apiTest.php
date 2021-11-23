<?php
include "./dev/pprint.php";
$username = "kolja";
require "./SleekDB/Store.php";
// phpinfo();
ini_set('display_startup_errors', 'On');
use SleekDB\Store;
use SleekDB\Query;


 
$databaseDirectory = __DIR__."/db";

// applying the store configuration is optional
$storeConfiguration = [
  "auto_cache" => true,
  "cache_lifetime" => null,
  "timeout" => 120,
  "primary_key" => "_id",
  "search" => [
    "min_length" => 2,
    "mode" => "or",
    "score_key" => "scoreKey",
    "algorithm" => Query::SEARCH_ALGORITHM["hits"]
  ]
];

// creating a new store object
$ItemStore = new Store($username, $databaseDirectory, $storeConfiguration);

 
//
// create random items
//
// $items =[];
// for ($i=0; $i < 255; $i++){
//   $rLang = rLang();
//     $items[] = array(
//       "titel" => "Code_".$i,
//       "date_cre" => rDate()['cre'],
//       "date_mod" => rDate()['mod'],
//       "lang" =>$rLang,
//       "tags" => rTags(),
//       "code" => rCode($rLang),
//       "desc" => file_get_contents('https://loripsum.net/generate.php?p=1&l=short&d=1&a=1&ul=1&ol=1&dl=1&bq=1&pr=1'),
//     );
// }

// $items = $ItemStore->insertMany($items);
// header("Content-Type: application/json");
// echo json_encode($items);
// pprint($items);


//
// search Items
//
$page = 1;
$limit = 10;
$skip = ($page - 1) * $limit;

$result = $ItemStore->createQueryBuilder()
  ->where([
    ["tags", "IN", ["CSS", "JS"]] 
  ])
  ->orderBy(["_id" => "DESC"])
 
  ->getQuery()
  ->fetch();

// Output
header("Content-Type: application/json");
echo json_encode($result);
// pprint($result);












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