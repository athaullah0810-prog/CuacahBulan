#include <WiFi.h>
#include <HTTPClient.h>

// --- KONFIGURASI WIFI ---
const char* ssid = "bulan0";      // Ganti dengan nama WiFi/Hotspot Anda
const char* password = "bulan123";  // Ganti dengan password WiFi Anda

// --- KONFIGURASI SERVER XAMPP ---
// Ganti 192.168.1.XX dengan IPv4 Komputer Server Anda (Cek via 'ipconfig' di CMD)
const char* serverUrl = "";

void setup() {
  Serial.begin(115200);
  delay(1000);

  Serial.println("\n--- AeroSense IoT ESP32 Initializing ---");
  
  // Menghubungkan ke Wi-Fi
  WiFi.begin(ssid, password);
  Serial.print("Menghubungkan ke WiFi");
  
  while (WiFi.status() != WL_CONNECTED) {
    delay(500);
    Serial.print(".");
  }
  
  Serial.println("\nWiFi Berhasil Terhubung!");
  Serial.print("IP Address ESP32: ");
  Serial.println(WiFi.localIP());
}

void loop() {
  // Cek status koneksi Wi-Fi
  if (WiFi.status() == WL_CONNECTED) {
    HTTPClient http;

    // Membuka koneksi ke endpoint API XAMPP
    http.begin(serverUrl);
    http.addHeader("Content-Type", "application/x-www-form-urlencoded");

    // ==========================================
    // SIMULASI / PEMBACAAN SENSOR ASLI
    // (Ganti nilai di bawah dengan variabel dari pembacaan sensor DHT22, BMP280, dll.)
    // ==========================================
    float suhuVal      = 28.4;
    float kelembabanVal= 68.5;
    float tekananVal   = 1012.3;
    float anginVal     = 14.2;
    float bateraiVal   = 85.0;
    float voltaseVal   = 4.10;
    int   mqVal        = 35;

   String apiKey = "AERO_IOT_2026_RAHASIA";

String httpRequestData = "api_key=" + apiKey +
                         "&suhu=" + String(suhuVal) +
                         "&kelembaban=" + String(kelembabanVal) +
                         "&tekanan=" + String(tekananVal) +
                         "&angin=" + String(anginVal) +
                         "&baterai=" + String(bateraiVal) +
                         "&voltase=" + String(voltaseVal) +
                         "&mq=" + String(mqVal);

    Serial.println("\nMengirim data ke server...");
    
    // Kirim data menggunakan metode HTTP POST
    int httpResponseCode = http.POST(httpRequestData);

    if (httpResponseCode > 0) {
      String response = http.getString();
      Serial.println("HTTP Response code: " + String(httpResponseCode));
      Serial.println("Respons dari Server: " + response);
    } else {
      Serial.print("Gagal mengirim data, Error code: ");
      Serial.println(httpResponseCode);
    }

    // Tutup koneksi HTTP
    http.end();
  } else {
    Serial.println("WiFi Terputus! Mencoba menghubungkan ulang...");
    WiFi.begin(ssid, password);
  }

  // Kirim data setiap 10 detik sekali (sesuaikan kebutuhan)
  delay(10000); 
}