# 🎨 UI/UX Design System - UtentiCarrello

## 📋 Panoramica
Design minimale, pulito e professionale senza fronzoli o effetti superflui.

## 🎯 Principi di Design

### 1. Semplicità
- Layout lineare e intuitivo
- Nessun elemento decorativo superfluo
- Focus sulla funzionalità

### 2. Chiarezza
- Tipografia leggibile (Inter, 400-600 weight)
- Spaziatura generosa tra elementi
- Gerarchia visiva chiara

### 3. Professionalità
- Palette neutra e pulita
- Transizioni discrete (200ms)
- Codice ben organizzato e commentato

---

## 🎨 Palette Colori

### Colori Principali
```css
--background: #ffffff      /* Sfondo principale */
--surface: #f9fafb        /* Superfici secondarie */
--accent: #2563eb         /* Blu principale (CTA, link attivi) */
--accent-hover: #1d4ed8   /* Blu scuro (hover state) */
```

### Colori Testo
```css
--text-primary: #1f2937   /* Testo principale */
--text-secondary: #6b7280 /* Testo secondario */
--text-muted: #9ca3af     /* Testo disattivato */
```

### Bordi e Divider
```css
--border: #e5e7eb         /* Bordi standard */
--border-light: #f3f4f6   /* Bordi chiari */
```

---

## 📐 Spaziatura

### Sistema di Spaziatura (rem)
- **XS**: 0.5rem (8px) - Gap tra elementi vicini
- **SM**: 1rem (16px) - Padding interno componenti
- **MD**: 1.5rem (24px) - Spaziatura tra sezioni
- **LG**: 2rem (32px) - Padding contenitori
- **XL**: 3rem (48px) - Margini tra macro-sezioni
- **2XL**: 4rem (64px) - Spaziatura hero/footer

---

## 🔤 Tipografia

### Font Family
```css
font-family: 'Inter', Arial, sans-serif;
```

### Scale Tipografica
```css
/* Titoli */
H1: 3rem (48px) / font-weight: 600
H2: 2rem (32px) / font-weight: 600
H3: 1.5rem (24px) / font-weight: 600

/* Corpo */
Body Large: 1.25rem (20px) / font-weight: 400
Body: 1rem (16px) / font-weight: 400
Small: 0.95rem (15px) / font-weight: 400
```

### Line Height
- Titoli: 1.2
- Corpo: 1.6
- Link: 1.5

---

## 🧩 Componenti

### Header
```
- Altezza: auto (padding 1rem)
- Background: #ffffff
- Border-bottom: 1px solid #e5e7eb
- Position: sticky top 0
```

### Hero Section
```
- Background: #f9fafb
- Padding: 5rem 2rem
- Text-align: center
- Max-width: 800px (content)
```

### Pulsanti

#### Primario (CTA)
```css
background: #2563eb
color: #ffffff
padding: 0.875rem 2rem
border-radius: 4px
font-size: 1.125rem
transition: background 0.2s ease

/* Hover */
background: #1d4ed8
```

#### Secondario
```css
background: #ffffff
color: #2563eb
border: 1px solid #2563eb
padding: 0.875rem 2rem
border-radius: 4px
font-size: 1.125rem
transition: all 0.2s ease

/* Hover */
background: #eff6ff
```

### Card (Opzionale)
```css
background: #f9fafb
border: 1px solid #e5e7eb
border-radius: 4px
padding: 2rem
transition: transform 0.2s ease

/* Hover */
transform: translateY(-2px)
```

### Footer
```
- Background: #f9fafb
- Border-top: 1px solid #e5e7eb
- Padding: 2rem 1.5rem
- Text-align: center
- Color: #6b7280
```

---

## 📱 Responsive Design

### Breakpoints
```css
/* Mobile */
@media (max-width: 768px)

/* Tablet */
@media (max-width: 1024px)

/* Desktop */
@media (min-width: 1025px)
```

### Adattamenti Mobile
- Menu navigazione: flex-direction column
- Hero title: font-size ridotto a 2rem
- Padding ridotto: 3rem → 1.5rem
- Pulsanti CTA: width 100% (max 300px)

---

## ♿ Accessibilità

### Contrasto Colori
- Testo principale su sfondo bianco: 7:1+ (AAA)
- Testo secondario: 4.5:1+ (AA)
- Link attivi: colore distinguibile

### Navigazione
- Focus states visibili (outline)
- Tab navigation funzionante
- Link semantici (<a>, <router-link>)

### Immagini
- Alt text obbligatorio
- Logo: alt="Logo UtentiCarrello"

---

## 🗂️ Struttura File

```
resources/js/
├── components/
│   ├── Header.vue      # Navigazione principale
│   ├── Hero.vue        # Sezione presentazione
│   └── Footer.vue      # Informazioni copyright
├── pages/
│   └── Home.vue        # Pagina home (composizione componenti)
└── stores/
    └── auth.js         # Store autenticazione

resources/css/
└── app.css             # Stili globali + Tailwind config

resources/views/
└── welcome.blade.php   # Template Laravel (carica Vue app)
```

---

## 📝 Convenzioni Codice

### Commenti HTML
```vue
<!-- Descrizione sezione in italiano -->
<section class="hero">
  <!-- Contenuto -->
</section>
```

### Commenti CSS
```css
/* Titolo sezione */
.hero-title {
  /* Proprietà */
}
```

### Indentazione
- 2 spazi per HTML/Vue template
- 2 spazi per CSS
- 2 spazi per JavaScript

---

## ✅ Checklist Qualità

- [ ] Codice HTML ben indentato
- [ ] Commenti in italiano chiari
- [ ] Nessun elemento decorativo superfluo
- [ ] Palette colori neutra applicata
- [ ] Font Inter caricato da Google Fonts
- [ ] Layout responsive testato (mobile/desktop)
- [ ] Contrasto colori accessibile (WCAG AA)
- [ ] Navigazione keyboard funzionante
- [ ] Alt text su tutte le immagini
- [ ] Transizioni discrete (200ms max)
- [ ] Componenti separati e riutilizzabili

---

## 🚀 Quick Start

### Avvio Server
```bash
./start.sh
```

### Struttura Pagina Home
1. **Header**: Logo + Menu navigazione
2. **Hero**: Titolo + Descrizione + CTA
3. **Footer**: Copyright + Link

### Modifiche Future
Per aggiungere sezioni, decommentare il blocco `features-section` in `Home.vue` e personalizzare.

---

**Design by**: UtentiCarrello Team  
**Last Updated**: 16 Ottobre 2025  
**Version**: 1.0.0 - Minimal & Clean
