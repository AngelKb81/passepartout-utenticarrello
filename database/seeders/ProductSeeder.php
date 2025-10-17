<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ProductSeeder extends Seeder
{
    /**
     * Popola la tabella products con prodotti di esempio.
     * Utilizza dati realistici per simulare un catalogo e-commerce.
     */
    public function run(): void
    {
        $products = [
            [
                'nome' => 'Smartphone Samsung Galaxy S24',
                'codice' => 'SAMSUNG-S24-001',
                'categoria' => 'Smartphone',
                'descrizione' => 'Smartphone di ultima generazione con fotocamera da 200MP, display Dynamic AMOLED 2X da 6.8" e processore Snapdragon 8 Gen 3. Perfetto per fotografia professionale e gaming.',
                'prezzo' => 999.99,
                'immagine' => 'products/smartphone-samsung-s24.jpg',
                'attivo' => true,
                'scorte' => 25,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nome' => 'Laptop Dell XPS 13',
                'codice' => 'DELL-XPS13-001',
                'categoria' => 'Computer',
                'descrizione' => 'Ultrabook premium con Intel Core i7, 16GB RAM, SSD 512GB. Display InfinityEdge da 13.4" 4K. Ideale per produttività e lavoro professionale.',
                'prezzo' => 1299.99,
                'immagine' => 'products/laptop-dell-xps13.jpg',
                'attivo' => true,
                'scorte' => 15,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nome' => 'Cuffie Wireless Sony WH-1000XM5',
                'codice' => 'SONY-WH1000XM5-001',
                'categoria' => 'Audio',
                'descrizione' => 'Cuffie over-ear con cancellazione attiva del rumore leader del settore. Autonomia 30 ore, ricarica rapida e qualità audio Hi-Res.',
                'prezzo' => 349.99,
                'immagine' => 'products/cuffie-sony-wh1000xm5.jpg',
                'attivo' => true,
                'scorte' => 40,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nome' => 'Tablet Apple iPad Pro 12.9"',
                'codice' => 'APPLE-IPADPRO-129-001',
                'categoria' => 'Tablet',
                'descrizione' => 'Tablet professionale con chip M2, display Liquid Retina XDR da 12.9", supporto Apple Pencil. Perfetto per creativi e professionisti.',
                'prezzo' => 1199.99,
                'immagine' => 'products/tablet-ipad-pro.jpg',
                'attivo' => true,
                'scorte' => 12,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nome' => 'Smartwatch Garmin Fenix 7',
                'codice' => 'GARMIN-FENIX7-001',
                'categoria' => 'Wearable',
                'descrizione' => 'Orologio GPS multisport con mappatura, monitoraggio salute avanzato, autonomia fino a 18 giorni. Resistente e perfetto per outdoor.',
                'prezzo' => 599.99,
                'immagine' => 'products/smartwatch-garmin-fenix7.jpg',
                'attivo' => true,
                'scorte' => 18,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nome' => 'Fotocamera Mirrorless Canon EOS R6',
                'codice' => 'CANON-EOSR6-001',
                'categoria' => 'Fotocamere',
                'descrizione' => 'Fotocamera mirrorless da 20.1MP con stabilizzazione a 5 assi, video 4K 60fps, autofocus Dual Pixel CMOS AF II. Per fotografi esigenti.',
                'prezzo' => 2199.99,
                'immagine' => 'products/fotocamera-canon-eosr6.jpg',
                'attivo' => true,
                'scorte' => 8,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nome' => 'Console PlayStation 5',
                'codice' => 'SONY-PS5-001',
                'categoria' => 'Gaming',
                'descrizione' => 'Console di gioco di nuova generazione con SSD ultra-veloce, ray tracing hardware, audio 3D Tempest. Include controller DualSense.',
                'prezzo' => 549.99,
                'immagine' => 'products/console-ps5.jpg',
                'attivo' => true,
                'scorte' => 6,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nome' => 'Router WiFi 6 ASUS AX6000',
                'codice' => 'ASUS-AX6000-001',
                'categoria' => 'Networking',
                'descrizione' => 'Router WiFi 6 dual-band ad alte prestazioni, velocità fino a 6000 Mbps, 8 antenne, AiMesh support. Ideale per smart home e gaming.',
                'prezzo' => 299.99,
                'immagine' => 'products/router-asus-ax6000.jpg',
                'attivo' => true,
                'scorte' => 22,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nome' => 'Monitor Gaming LG UltraGear 27"',
                'codice' => 'LG-ULTRAGEAR27-001',
                'categoria' => 'Monitor',
                'descrizione' => 'Monitor gaming QHD da 27" con refresh rate 165Hz, tempo di risposta 1ms, G-SYNC compatible, HDR10. Perfetto per gaming competitivo.',
                'prezzo' => 399.99,
                'immagine' => 'products/monitor-lg-ultragear.jpg',
                'attivo' => true,
                'scorte' => 14,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nome' => 'Altoparlante Smart Amazon Echo Studio',
                'codice' => 'AMAZON-ECHOSTUDIO-001',
                'categoria' => 'Audio',
                'descrizione' => 'Smart speaker premium con audio spaziale Dolby Atmos, 5 altoparlanti direzionali, integrazione Alexa e controllo smart home.',
                'prezzo' => 199.99,
                'immagine' => 'products/speaker-echo-studio.jpg',
                'attivo' => true,
                'scorte' => 30,
                'created_at' => now(),
                'updated_at' => now(),
            ]
        ];

        DB::table('products')->insert($products);
    }
}
