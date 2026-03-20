# Hajusrakendused

Laravel 11 veebirakendus, mis on valminud kooliprojektina ja sisaldab 5 integreeritud funktsionaalsuse moodulit.

## Live Demo
> Lisa siia oma rakenduse URL pärast üleslaadimist

## Moodulid

### 1. 🌤️ Ilmateenuse API
- Pärib reaalajas ilmaandmeid OpenWeatherMap API kaudu
- Linnade otsimise võimalus
- 10-minutiline lokaalne vahemälu (cache) API päringute vähendamiseks
- Kuvab temperatuuri, niiskuse, tuule kiiruse, õhurõhu ja ilmaikoonid

### 2. 🗺️ Kaardirakendus
- Interaktiivne kaart Leaflet.js + OpenStreetMap abil (tasuta, API võtit pole vaja)
- Täielik markerite haldus (lisamine, vaatamine, muutmine, kustutamine)
- Klõpsa kaardil, et lisada uus marker
- Kõik markerid salvestatakse andmebaasi ja kuvatakse lehe laadimisel

### 3. 📝 Blogi ja kommentaarid
- Täielik autentimissüsteem (registreerimine, sisselogimine, väljalogimine)
- Blogipostituste haldus (lisamine, muutmine, kustutamine, vaatamine)
- Kommentaaride süsteem igale postitusele
- Ainult postituse autor saab oma postitusi muuta/kustutada
- Administraator saab kustutada kõiki kommentaare

### 4. 🛍️ E-pood
- 9 toodet koos piltide, nimede, hindade ja kirjeldustega
- Ostukorv (lisamine, koguse muutmine, eemaldamine)
- Kassavorm (eesnimi, perenimi, e-post, telefon)
- Stripe makseintegratsioon (testkeskkonnas)
- Tellimused salvestatakse andmebaasi pärast edukat makset
- Õnnestunud ja ebaõnnestunud makse käsitlemine

### 5. ⚔️ Warframe Alerts API
- Pärib reaalajas mänguandmeid ametlikust Warframe API-st (api.warframestat.us)
- Kuvab aktiivseid hoiatusi, sündmusi ja invasioone
- Andmed salvestatakse lokaalses andmebaasis ja uuendatakse iga 10 minuti järel
- Filtreerimine tüübi (hoiatus/sündmus/invasioon) ja fraktsiooni järgi
- Otsingu- ja sorteerimisvõimalus
- JSON API lõpp-punkt aadressil `/api/warframe` koos piirangu, filtri ja otsinguga

## Kasutatud tehnoloogiad

- **Backend:** Laravel 11 (PHP 8.3)
- **Frontend:** Vue 3 + Inertia.js
- **Stiil:** Tailwind CSS
- **Ehitusvahend:** Vite
- **Andmebaas:** SQLite
- **Autentimine:** Laravel Breeze
- **Kaardid:** Leaflet.js + OpenStreetMap
- **Maksed:** Stripe
- **API-d:** OpenWeatherMap, Warframe API (api.warframestat.us)
- **Versioonihaldus:** Git + GitHub

## Nõuded

- PHP 8.2+
- Composer
- Node.js 18+
- NPM

## Paigaldamine ja käivitamine

### 1. Klooni repositoorium
```bash
git clone https://github.com/jyrgen112/Hajusrakendused.git
cd Hajusrakendused
```

### 2. Paigalda sõltuvused
```bash
composer install
npm install
```

### 3. Seadista keskkond
```bash
cp .env.example .env
php artisan key:generate
```

### 4. Lisa API võtmed `.env` faili
```
OPENWEATHER_API_KEY=sinu_openweathermap_võti
STRIPE_KEY=sinu_stripe_avalik_võti
STRIPE_SECRET=sinu_stripe_salajane_võti
```

### 5. Seadista andmebaas
```bash
php artisan migrate
php artisan db:seed --class=ProductSeeder
```

### 6. Käivita rakendus
Ava kaks terminali akent:

**Terminal 1 — Laravel server:**
```bash
php artisan serve
```

**Terminal 2 — Vite varad:**
```bash
npm run dev
```

Seejärel ava brauser aadressil **http://127.0.0.1:8000**

## Stripe maksete testimine

Kasuta kassalehel järgmisi testkaardiandmeid:
- **Kaardi number:** 4242 4242 4242 4242
- **Aegumiskuupäev:** mis tahes tuleviku kuupäev (nt 12/34)
- **CVC:** mis tahes 3 numbrit (nt 123)

## Projekti struktuur
```
app/
├── Http/Controllers/
│   ├── WeatherController.php
│   ├── MarkerController.php
│   ├── PostController.php
│   ├── CommentController.php
│   ├── ShopController.php
│   ├── CartController.php
│   ├── CheckoutController.php
│   └── WarframeController.php
├── Models/
│   ├── Marker.php
│   ├── Post.php
│   ├── Comment.php
│   ├── Product.php
│   ├── Order.php
│   ├── OrderItem.php
│   └── WarframeAlert.php
resources/js/Pages/
├── Welcome.vue          # Avalehe hub
├── Weather/Index.vue    # Moodul 1
├── Map/Index.vue        # Moodul 2
├── Blog/Index.vue       # Moodul 3
├── Shop/                # Moodul 4
│   ├── Index.vue
│   ├── Cart.vue
│   ├── Checkout.vue
│   └── Success.vue
└── Warframe/Index.vue   # Moodul 5
```

## Autor
Jürgen — kooliprojekt Hajusrakendused kursuse raames
