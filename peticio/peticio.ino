#include <WiFi.h>
#include <HTTPClient.h>
#include <SoftwareSerial.h>

// INCLUDES PER A LA COMUNICACIÓ AMB L'ARDUINO
// I LA MANIPULACIÓ DE CADENES DE CARÀCTERS
#include <StringSplitter.h>

// Creem dues variables que conten la xarxa i la contrasenya d’accés
const char* ssid = "Aula110";
const char* password = "aula110aula110";

// Variable que conté l’adreça IP del nostre servidor
String serverName="http://192.168.110.165:80";

// Variable que contindrà el codi de resposta de la petició HTTP
int httpResponseCode;

// Variable que contindrà l’adreça URL de la petició HTTP
String serverPath;

// Variable per a enviar les respostes a l'Arduino
// mitjançant comunicació sèrie
SoftwareSerial ESP_Serial(32,33);

// Definim una variable de caràcter i unes
// variables de cadena per a l'enviament de dades
char c;
String str, peticio, url;

// Configura la IP fija (ajusta los valores a tu red)
// IPAddress local_IP(192, 168, 110, 199);
// IPAddress gateway(192, 168, 110, 1);
// IPAddress subnet(255, 255, 255, 0);
// IPAddress primaryDNS(8, 8, 8, 8);   // Opcional
// IPAddress secondaryDNS(8, 8, 4, 4); // Opcional

void setup() {
  // Iniciem la connexió serial per a enviar informació de control
  Serial.begin(115200);
  delay(10);
  // Definim el Serial per on enviarem les
  // dades a l’Arduino
  ESP_Serial.begin(9600);
  // Imprimim informació de connexió a la xarxa
  Serial.println();
  Serial.print("Conectandose a red : ");
  Serial.println(ssid);
    // Establece la IP fija antes de conectar
  // if (!WiFi.config(local_IP, gateway, subnet, primaryDNS, secondaryDNS)) {
  //   Serial.println("❌ Error al configurar la IP estática");
  // }
  
  // Iniciem el mòdul de connexió WIFI amb el SSID i la contrasenya
  WiFi.begin(ssid, password); 
  
  // Mentre la connexió no estigui activa
  while (WiFi.status() != WL_CONNECTED)
  {
    // Esperem a que ho estigui
    delay(500);
    Serial.print(".");
  }

  // Quan la connexió ja estigui establerta imprimim la informació
  // corresponent i l’adreça IP assignada
  Serial.println("");
  Serial.println("WiFi conectado");
  // Mostrem l’adreça IP que tenim assignada
  Serial.println("Tenim la següent IP:");
  Serial.println(WiFi.localIP());
}

void loop() 
{
  // La placa ESP es trobarà a l'espera de rebre peticions
  // des de l'Arduino
  peticio ="0";
  // Mentre no hi hagi cap petició estarem a l'espera
  while (peticio == "0") {
    //peticio = llegir_dades_arduino();
  }
  // Si finalitza el WHILE és perquè hem rebut una petició
  // des de l'Arduino
  c=0;
  str="";
  // Ja tinc la peticio - La mostro pel serial per a informar
  // de la rebuda
  Serial.println("He rebut una nova petició: " + peticio);

  // Ara he d'enviar la petició al servidor per a generar la resposta
  // i retornar-la a l'Arduino
  // La petició està dividida en 2 parts dividies per un -:
  // 1a - La URL que enviaré al servidor
  // 2a - El codi de final de petició, que és un salt de línia (\n)
  StringSplitter *splitter = new StringSplitter(peticio, '-', 2);
  // Prenem la primera part
  url = splitter->getItemAtIndex(0);

  // Ara envíem la petició al servidor
  if(WiFi.status()== WL_CONNECTED){
    // Creem un client WiFi i un Client HTTP per a fer les peticions
    WiFiClient client;
    HTTPClient http;

    // Definim la petició unint el nom del servidor amb la petició
    // En aquest cas enviem la petició q=1
    serverPath = serverName + "/index.php?" + peticio;
    // Informem de quina consulta faré al servidor
    Serial.println("Fare la petició completa a aquesta URL: " + serverPath);
      
    // Llencem la petició HTTP amb el client sobre el nostre servidor
    http.begin(client, serverPath.c_str());
       
    // Obtenim el codi de resposta de la petició.  Si és positiu és que, en general,
    // tot ha funcionat bé
    httpResponseCode = http.GET();
      
    if (httpResponseCode > 0) {
      // Imprimim el codi de resposta de la petició
      Serial.print("Codi de resposta HTTP: ");
      Serial.println(httpResponseCode);
      
      // Obtenim el text resultat de la nostra petició
      String payload = http.getString();

      // Informem de la resposta del servidor
      Serial.println("He rebut aquesta resposta del servidor: " + payload);
      Serial.println("Serà la resposta que enviaré a l'ARDUINO");
      Serial.println(payload);
      // Enviem les dades de tornada a l'Arduino
      enviar_dades_arduino(payload + '\n');

    } else {
      // S'ha produit un error - Informem de l'error
      Serial.print("Codi d'error HTTP: ");
      Serial.println(httpResponseCode);
    }
    // Alliberem el recurs HTTP
    http.end();
  }
  //while (0 == 0) { delay(1000); };
  delay(1000);
}


// Funció d'enviament de dades a l'Arduino
void enviar_dades_arduino(String dades) {
  ESP_Serial.print(dades);
}

// Funció que llegeix les dades que envia l'Arduino
String llegir_dades_arduino() {
  // Llegim les dades caràcter a caràcter
  while(ESP_Serial.available()>0) {
    // Llegim un caràcter i, si no és \n,
    // l’afegim a la cadena
    c = ESP_Serial.read();
    if (c=='\n') {
      break;
    } else {
      str+=c;
    }
  }
  if (c=='\n') {
    // Hem obtingut una petició perquè hem arribat al caràcter
    // de fí de petició que es el salt de línia (\n)
    // Retornem la petició rebuda
    return str;
  }
  // Si no estem rebent dades de l'Arduino retornem la cadena "0"
  // fet que indica que no estem rebent peticions
  return "0";
}