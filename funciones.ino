
String funcio_peticio_web_simplificada(peticio){
  Serial.println("Enviando UID a ESP32: " + peticio);
  peticio.concat("\n");
  enviar_dades_arduino(peticio);
  resultat = "0";
  while (resultat == "0") {
    resultat = llegir_dades_arduino();
  }
  c = 0;
  str = "";
  return resultat;
}
