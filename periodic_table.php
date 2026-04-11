<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Periodic Table - The Thinking Mind</title>
    <link rel="stylesheet" href="css/style.css">
    <style>
        .pt-wrapper { padding: 24px 16px; }

        /* Search */
        .pt-search-row {
            display: flex;
            gap: 12px;
            align-items: center;
            margin-bottom: 20px;
            flex-wrap: wrap;
        }
        .pt-search-row input {
            flex: 1;
            min-width: 200px;
            padding: 10px 14px;
            border: 2px solid #ddd;
            border-radius: var(--border-radius);
            font-size: 0.95em;
            transition: border-color 0.2s;
        }
        .pt-search-row input:focus { outline: none; border-color: var(--primary-color); }

        /* Legend */
        .pt-legend {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
            margin-bottom: 16px;
            font-size: 0.75em;
        }
        .legend-item {
            display: flex;
            align-items: center;
            gap: 5px;
        }
        .legend-dot {
            width: 12px; height: 12px;
            border-radius: 3px;
            flex-shrink: 0;
        }

        /* Table grid */
        .pt-grid {
            display: grid;
            grid-template-columns: repeat(18, minmax(46px, 1fr));
            gap: 3px;
            overflow-x: auto;
        }
        .pt-cell {
            aspect-ratio: 1;
            border-radius: 6px;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: transform 0.15s, box-shadow 0.15s;
            padding: 2px;
            border: 1px solid transparent;
            min-width: 46px;
        }
        .pt-cell:hover {
            transform: scale(1.15);
            z-index: 10;
            box-shadow: 0 4px 16px rgba(0,0,0,0.2);
            border-color: rgba(0,0,0,0.15);
        }
        .pt-cell.dimmed { opacity: 0.25; }
        .pt-cell .pt-num { font-size: 0.55em; color: rgba(0,0,0,0.5); align-self: flex-start; padding-left: 3px; line-height: 1.2; }
        .pt-cell .pt-sym { font-size: 1em; font-weight: 700; line-height: 1; }
        .pt-cell .pt-name { font-size: 0.45em; text-align: center; line-height: 1.2; overflow: hidden; white-space: nowrap; max-width: 100%; }
        .pt-cell .pt-mass { font-size: 0.42em; color: rgba(0,0,0,0.5); }
        .pt-spacer { /* empty grid cell */ }

        /* Category colours */
        .cat-alkali       { background: #ffccbc; }
        .cat-alkaline     { background: #fff9c4; }
        .cat-transition   { background: #b3e5fc; }
        .cat-post         { background: #c8e6c9; }
        .cat-metalloid    { background: #dcedc8; }
        .cat-nonmetal     { background: #f8bbd0; }
        .cat-halogen      { background: #e1bee7; }
        .cat-noble        { background: #cfd8dc; }
        .cat-lanthanide   { background: #ffe0b2; }
        .cat-actinide     { background: #d7ccc8; }
        .cat-unknown      { background: #eeeeee; }

        /* Detail panel */
        .pt-detail {
            display: none;
            position: fixed;
            top: 50%; left: 50%;
            transform: translate(-50%, -50%);
            background: white;
            border-radius: var(--border-radius);
            box-shadow: 0 8px 40px rgba(0,0,0,0.25);
            padding: 32px;
            z-index: 1000;
            min-width: 300px;
            max-width: 420px;
            width: 90%;
        }
        .pt-detail.visible { display: block; }
        .pt-detail-header {
            display: flex;
            align-items: center;
            gap: 20px;
            margin-bottom: 20px;
        }
        .pt-detail-symbol {
            font-size: 3em;
            font-weight: 800;
            width: 72px; height: 72px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }
        .pt-detail-title h2 { margin: 0; font-size: 1.4em; }
        .pt-detail-title p { margin: 2px 0 0; color: #666; font-size: 0.88em; }
        .pt-detail table { width: 100%; border-collapse: collapse; font-size: 0.9em; }
        .pt-detail table tr td { padding: 7px 4px; border-bottom: 1px solid #f0f0f0; }
        .pt-detail table tr td:first-child { color: #888; width: 45%; }
        .pt-detail table tr td:last-child { font-weight: 600; }
        .pt-close {
            position: absolute;
            top: 12px; right: 16px;
            background: none; border: none;
            font-size: 1.4em; cursor: pointer;
            color: #999;
        }
        .pt-close:hover { color: #333; }
        .pt-overlay {
            display: none;
            position: fixed; inset: 0;
            background: rgba(0,0,0,0.4);
            z-index: 999;
        }
        .pt-overlay.visible { display: block; }
    </style>
</head>
<body>
<div class="container">
    <header>
        <h1>⚗️ Periodic Table</h1>
        <p class="subtitle">Click any element to view its details</p>
        <div class="header-buttons">
            <a href="tools_utilities.php" class="btn btn-back">Back to Tools</a>
        </div>
    </header>

    <main>
        <div class="pt-wrapper">
            <div class="pt-search-row">
                <input type="text" id="pt-search" placeholder="Search by name, symbol or atomic number…" oninput="filterTable()" />
            </div>

            <div class="pt-legend" id="pt-legend"></div>
            <div class="pt-grid" id="pt-grid"></div>
        </div>
    </main>

    <footer>
        <p>&copy; 2026 The Thinking Mind | Cultivating Excellence in Learning</p>
    </footer>
</div>

<!-- Detail panel -->
<div class="pt-overlay" id="pt-overlay" onclick="closeDetail()"></div>
<div class="pt-detail" id="pt-detail">
    <button class="pt-close" onclick="closeDetail()">✕</button>
    <div class="pt-detail-header">
        <div class="pt-detail-symbol" id="dd-sym-box"></div>
        <div class="pt-detail-title">
            <h2 id="dd-name"></h2>
            <p id="dd-cat"></p>
        </div>
    </div>
    <table>
        <tr><td>Atomic Number</td><td id="dd-num"></td></tr>
        <tr><td>Symbol</td><td id="dd-sym"></td></tr>
        <tr><td>Atomic Mass</td><td id="dd-mass"></td></tr>
        <tr><td>Period</td><td id="dd-period"></td></tr>
        <tr><td>Group</td><td id="dd-group"></td></tr>
        <tr><td>State (room temp)</td><td id="dd-state"></td></tr>
        <tr><td>Electron Config</td><td id="dd-config"></td></tr>
    </table>
</div>

<script>
const CATEGORIES = {
    'alkali':     { label: 'Alkali Metal',       cls: 'cat-alkali' },
    'alkaline':   { label: 'Alkaline Earth',      cls: 'cat-alkaline' },
    'transition': { label: 'Transition Metal',    cls: 'cat-transition' },
    'post':       { label: 'Post-transition',     cls: 'cat-post' },
    'metalloid':  { label: 'Metalloid',           cls: 'cat-metalloid' },
    'nonmetal':   { label: 'Nonmetal',            cls: 'cat-nonmetal' },
    'halogen':    { label: 'Halogen',             cls: 'cat-halogen' },
    'noble':      { label: 'Noble Gas',           cls: 'cat-noble' },
    'lanthanide': { label: 'Lanthanide',          cls: 'cat-lanthanide' },
    'actinide':   { label: 'Actinide',            cls: 'cat-actinide' },
    'unknown':    { label: 'Unknown',             cls: 'cat-unknown' },
};

// [num, sym, name, mass, cat, period, group, state, config]
const ELEMENTS = [
  [1,'H','Hydrogen','1.008','nonmetal',1,1,'Gas','1s¹'],
  [2,'He','Helium','4.003','noble',1,18,'Gas','1s²'],
  [3,'Li','Lithium','6.941','alkali',2,1,'Solid','[He] 2s¹'],
  [4,'Be','Beryllium','9.012','alkaline',2,2,'Solid','[He] 2s²'],
  [5,'B','Boron','10.811','metalloid',2,13,'Solid','[He] 2s² 2p¹'],
  [6,'C','Carbon','12.011','nonmetal',2,14,'Solid','[He] 2s² 2p²'],
  [7,'N','Nitrogen','14.007','nonmetal',2,15,'Gas','[He] 2s² 2p³'],
  [8,'O','Oxygen','15.999','nonmetal',2,16,'Gas','[He] 2s² 2p⁴'],
  [9,'F','Fluorine','18.998','halogen',2,17,'Gas','[He] 2s² 2p⁵'],
  [10,'Ne','Neon','20.180','noble',2,18,'Gas','[He] 2s² 2p⁶'],
  [11,'Na','Sodium','22.990','alkali',3,1,'Solid','[Ne] 3s¹'],
  [12,'Mg','Magnesium','24.305','alkaline',3,2,'Solid','[Ne] 3s²'],
  [13,'Al','Aluminium','26.982','post',3,13,'Solid','[Ne] 3s² 3p¹'],
  [14,'Si','Silicon','28.086','metalloid',3,14,'Solid','[Ne] 3s² 3p²'],
  [15,'P','Phosphorus','30.974','nonmetal',3,15,'Solid','[Ne] 3s² 3p³'],
  [16,'S','Sulfur','32.065','nonmetal',3,16,'Solid','[Ne] 3s² 3p⁴'],
  [17,'Cl','Chlorine','35.453','halogen',3,17,'Gas','[Ne] 3s² 3p⁵'],
  [18,'Ar','Argon','39.948','noble',3,18,'Gas','[Ne] 3s² 3p⁶'],
  [19,'K','Potassium','39.098','alkali',4,1,'Solid','[Ar] 4s¹'],
  [20,'Ca','Calcium','40.078','alkaline',4,2,'Solid','[Ar] 4s²'],
  [21,'Sc','Scandium','44.956','transition',4,3,'Solid','[Ar] 3d¹ 4s²'],
  [22,'Ti','Titanium','47.867','transition',4,4,'Solid','[Ar] 3d² 4s²'],
  [23,'V','Vanadium','50.942','transition',4,5,'Solid','[Ar] 3d³ 4s²'],
  [24,'Cr','Chromium','51.996','transition',4,6,'Solid','[Ar] 3d⁵ 4s¹'],
  [25,'Mn','Manganese','54.938','transition',4,7,'Solid','[Ar] 3d⁵ 4s²'],
  [26,'Fe','Iron','55.845','transition',4,8,'Solid','[Ar] 3d⁶ 4s²'],
  [27,'Co','Cobalt','58.933','transition',4,9,'Solid','[Ar] 3d⁷ 4s²'],
  [28,'Ni','Nickel','58.693','transition',4,10,'Solid','[Ar] 3d⁸ 4s²'],
  [29,'Cu','Copper','63.546','transition',4,11,'Solid','[Ar] 3d¹⁰ 4s¹'],
  [30,'Zn','Zinc','65.38','transition',4,12,'Solid','[Ar] 3d¹⁰ 4s²'],
  [31,'Ga','Gallium','69.723','post',4,13,'Solid','[Ar] 3d¹⁰ 4s² 4p¹'],
  [32,'Ge','Germanium','72.630','metalloid',4,14,'Solid','[Ar] 3d¹⁰ 4s² 4p²'],
  [33,'As','Arsenic','74.922','metalloid',4,15,'Solid','[Ar] 3d¹⁰ 4s² 4p³'],
  [34,'Se','Selenium','78.971','nonmetal',4,16,'Solid','[Ar] 3d¹⁰ 4s² 4p⁴'],
  [35,'Br','Bromine','79.904','halogen',4,17,'Liquid','[Ar] 3d¹⁰ 4s² 4p⁵'],
  [36,'Kr','Krypton','83.798','noble',4,18,'Gas','[Ar] 3d¹⁰ 4s² 4p⁶'],
  [37,'Rb','Rubidium','85.468','alkali',5,1,'Solid','[Kr] 5s¹'],
  [38,'Sr','Strontium','87.62','alkaline',5,2,'Solid','[Kr] 5s²'],
  [39,'Y','Yttrium','88.906','transition',5,3,'Solid','[Kr] 4d¹ 5s²'],
  [40,'Zr','Zirconium','91.224','transition',5,4,'Solid','[Kr] 4d² 5s²'],
  [41,'Nb','Niobium','92.906','transition',5,5,'Solid','[Kr] 4d⁴ 5s¹'],
  [42,'Mo','Molybdenum','95.96','transition',5,6,'Solid','[Kr] 4d⁵ 5s¹'],
  [43,'Tc','Technetium','98','transition',5,7,'Solid','[Kr] 4d⁵ 5s²'],
  [44,'Ru','Ruthenium','101.07','transition',5,8,'Solid','[Kr] 4d⁷ 5s¹'],
  [45,'Rh','Rhodium','102.906','transition',5,9,'Solid','[Kr] 4d⁸ 5s¹'],
  [46,'Pd','Palladium','106.42','transition',5,10,'Solid','[Kr] 4d¹⁰'],
  [47,'Ag','Silver','107.868','transition',5,11,'Solid','[Kr] 4d¹⁰ 5s¹'],
  [48,'Cd','Cadmium','112.411','transition',5,12,'Solid','[Kr] 4d¹⁰ 5s²'],
  [49,'In','Indium','114.818','post',5,13,'Solid','[Kr] 4d¹⁰ 5s² 5p¹'],
  [50,'Sn','Tin','118.710','post',5,14,'Solid','[Kr] 4d¹⁰ 5s² 5p²'],
  [51,'Sb','Antimony','121.760','metalloid',5,15,'Solid','[Kr] 4d¹⁰ 5s² 5p³'],
  [52,'Te','Tellurium','127.60','metalloid',5,16,'Solid','[Kr] 4d¹⁰ 5s² 5p⁴'],
  [53,'I','Iodine','126.904','halogen',5,17,'Solid','[Kr] 4d¹⁰ 5s² 5p⁵'],
  [54,'Xe','Xenon','131.293','noble',5,18,'Gas','[Kr] 4d¹⁰ 5s² 5p⁶'],
  [55,'Cs','Caesium','132.905','alkali',6,1,'Solid','[Xe] 6s¹'],
  [56,'Ba','Barium','137.327','alkaline',6,2,'Solid','[Xe] 6s²'],
  [57,'La','Lanthanum','138.905','lanthanide',6,3,'Solid','[Xe] 5d¹ 6s²'],
  [58,'Ce','Cerium','140.116','lanthanide',6,null,'Solid','[Xe] 4f¹ 5d¹ 6s²'],
  [59,'Pr','Praseodymium','140.908','lanthanide',6,null,'Solid','[Xe] 4f³ 6s²'],
  [60,'Nd','Neodymium','144.242','lanthanide',6,null,'Solid','[Xe] 4f⁴ 6s²'],
  [61,'Pm','Promethium','145','lanthanide',6,null,'Solid','[Xe] 4f⁵ 6s²'],
  [62,'Sm','Samarium','150.36','lanthanide',6,null,'Solid','[Xe] 4f⁶ 6s²'],
  [63,'Eu','Europium','151.964','lanthanide',6,null,'Solid','[Xe] 4f⁷ 6s²'],
  [64,'Gd','Gadolinium','157.25','lanthanide',6,null,'Solid','[Xe] 4f⁷ 5d¹ 6s²'],
  [65,'Tb','Terbium','158.925','lanthanide',6,null,'Solid','[Xe] 4f⁹ 6s²'],
  [66,'Dy','Dysprosium','162.500','lanthanide',6,null,'Solid','[Xe] 4f¹⁰ 6s²'],
  [67,'Ho','Holmium','164.930','lanthanide',6,null,'Solid','[Xe] 4f¹¹ 6s²'],
  [68,'Er','Erbium','167.259','lanthanide',6,null,'Solid','[Xe] 4f¹² 6s²'],
  [69,'Tm','Thulium','168.934','lanthanide',6,null,'Solid','[Xe] 4f¹³ 6s²'],
  [70,'Yb','Ytterbium','173.054','lanthanide',6,null,'Solid','[Xe] 4f¹⁴ 6s²'],
  [71,'Lu','Lutetium','174.967','lanthanide',6,null,'Solid','[Xe] 4f¹⁴ 5d¹ 6s²'],
  [72,'Hf','Hafnium','178.49','transition',6,4,'Solid','[Xe] 4f¹⁴ 5d² 6s²'],
  [73,'Ta','Tantalum','180.948','transition',6,5,'Solid','[Xe] 4f¹⁴ 5d³ 6s²'],
  [74,'W','Tungsten','183.84','transition',6,6,'Solid','[Xe] 4f¹⁴ 5d⁴ 6s²'],
  [75,'Re','Rhenium','186.207','transition',6,7,'Solid','[Xe] 4f¹⁴ 5d⁵ 6s²'],
  [76,'Os','Osmium','190.23','transition',6,8,'Solid','[Xe] 4f¹⁴ 5d⁶ 6s²'],
  [77,'Ir','Iridium','192.217','transition',6,9,'Solid','[Xe] 4f¹⁴ 5d⁷ 6s²'],
  [78,'Pt','Platinum','195.084','transition',6,10,'Solid','[Xe] 4f¹⁴ 5d⁹ 6s¹'],
  [79,'Au','Gold','196.967','transition',6,11,'Solid','[Xe] 4f¹⁴ 5d¹⁰ 6s¹'],
  [80,'Hg','Mercury','200.59','transition',6,12,'Liquid','[Xe] 4f¹⁴ 5d¹⁰ 6s²'],
  [81,'Tl','Thallium','204.383','post',6,13,'Solid','[Xe] 4f¹⁴ 5d¹⁰ 6s² 6p¹'],
  [82,'Pb','Lead','207.2','post',6,14,'Solid','[Xe] 4f¹⁴ 5d¹⁰ 6s² 6p²'],
  [83,'Bi','Bismuth','208.980','post',6,15,'Solid','[Xe] 4f¹⁴ 5d¹⁰ 6s² 6p³'],
  [84,'Po','Polonium','209','metalloid',6,16,'Solid','[Xe] 4f¹⁴ 5d¹⁰ 6s² 6p⁴'],
  [85,'At','Astatine','210','halogen',6,17,'Solid','[Xe] 4f¹⁴ 5d¹⁰ 6s² 6p⁵'],
  [86,'Rn','Radon','222','noble',6,18,'Gas','[Xe] 4f¹⁴ 5d¹⁰ 6s² 6p⁶'],
  [87,'Fr','Francium','223','alkali',7,1,'Solid','[Rn] 7s¹'],
  [88,'Ra','Radium','226','alkaline',7,2,'Solid','[Rn] 7s²'],
  [89,'Ac','Actinium','227','actinide',7,3,'Solid','[Rn] 6d¹ 7s²'],
  [90,'Th','Thorium','232.038','actinide',7,null,'Solid','[Rn] 6d² 7s²'],
  [91,'Pa','Protactinium','231.036','actinide',7,null,'Solid','[Rn] 5f² 6d¹ 7s²'],
  [92,'U','Uranium','238.029','actinide',7,null,'Solid','[Rn] 5f³ 6d¹ 7s²'],
  [93,'Np','Neptunium','237','actinide',7,null,'Solid','[Rn] 5f⁴ 6d¹ 7s²'],
  [94,'Pu','Plutonium','244','actinide',7,null,'Solid','[Rn] 5f⁶ 7s²'],
  [95,'Am','Americium','243','actinide',7,null,'Solid','[Rn] 5f⁷ 7s²'],
  [96,'Cm','Curium','247','actinide',7,null,'Solid','[Rn] 5f⁷ 6d¹ 7s²'],
  [97,'Bk','Berkelium','247','actinide',7,null,'Solid','[Rn] 5f⁹ 7s²'],
  [98,'Cf','Californium','251','actinide',7,null,'Solid','[Rn] 5f¹⁰ 7s²'],
  [99,'Es','Einsteinium','252','actinide',7,null,'Solid','[Rn] 5f¹¹ 7s²'],
  [100,'Fm','Fermium','257','actinide',7,null,'Solid','[Rn] 5f¹² 7s²'],
  [101,'Md','Mendelevium','258','actinide',7,null,'Solid','[Rn] 5f¹³ 7s²'],
  [102,'No','Nobelium','259','actinide',7,null,'Solid','[Rn] 5f¹⁴ 7s²'],
  [103,'Lr','Lawrencium','266','actinide',7,null,'Solid','[Rn] 5f¹⁴ 7s² 7p¹'],
  [104,'Rf','Rutherfordium','267','transition',7,4,'Solid','[Rn] 5f¹⁴ 6d² 7s²'],
  [105,'Db','Dubnium','268','transition',7,5,'Solid','[Rn] 5f¹⁴ 6d³ 7s²'],
  [106,'Sg','Seaborgium','269','transition',7,6,'Solid','[Rn] 5f¹⁴ 6d⁴ 7s²'],
  [107,'Bh','Bohrium','270','transition',7,7,'Solid','[Rn] 5f¹⁴ 6d⁵ 7s²'],
  [108,'Hs','Hassium','269','transition',7,8,'Solid','[Rn] 5f¹⁴ 6d⁶ 7s²'],
  [109,'Mt','Meitnerium','278','unknown',7,9,'Solid','[Rn] 5f¹⁴ 6d⁷ 7s²'],
  [110,'Ds','Darmstadtium','281','unknown',7,10,'Solid','[Rn] 5f¹⁴ 6d⁸ 7s²'],
  [111,'Rg','Roentgenium','282','unknown',7,11,'Solid','[Rn] 5f¹⁴ 6d⁹ 7s²'],
  [112,'Cn','Copernicium','285','transition',7,12,'Liquid','[Rn] 5f¹⁴ 6d¹⁰ 7s²'],
  [113,'Nh','Nihonium','286','post',7,13,'Solid','[Rn] 5f¹⁴ 6d¹⁰ 7s² 7p¹'],
  [114,'Fl','Flerovium','289','post',7,14,'Solid','[Rn] 5f¹⁴ 6d¹⁰ 7s² 7p²'],
  [115,'Mc','Moscovium','290','post',7,15,'Solid','[Rn] 5f¹⁴ 6d¹⁰ 7s² 7p³'],
  [116,'Lv','Livermorium','293','post',7,16,'Solid','[Rn] 5f¹⁴ 6d¹⁰ 7s² 7p⁴'],
  [117,'Ts','Tennessine','294','halogen',7,17,'Solid','[Rn] 5f¹⁴ 6d¹⁰ 7s² 7p⁵'],
  [118,'Og','Oganesson','294','noble',7,18,'Gas','[Rn] 5f¹⁴ 6d¹⁰ 7s² 7p⁶'],
];

// Map: [period][group] = element index (0-based)
// Standard 18-column layout
const layout = {}; // layout[period][group] = element

ELEMENTS.forEach((el, i) => {
    const [num, sym, name, mass, cat, period, group] = el;
    if (group) {
        if (!layout[period]) layout[period] = {};
        layout[period][group] = i;
    }
});

// Lanthanides: period 8 (visual row), groups 4..17 mapped to elements 57-71 (La-Lu minus La at 3)
// We show lanthanide/actinide rows below with a gap row
const lanthanides = ELEMENTS.filter(e => e[4] === 'lanthanide');
const actinides   = ELEMENTS.filter(e => e[4] === 'actinide');

function buildLegend() {
    const legend = document.getElementById('pt-legend');
    Object.entries(CATEGORIES).forEach(([key, val]) => {
        legend.innerHTML += `<div class="legend-item"><div class="legend-dot ${val.cls}"></div><span>${val.label}</span></div>`;
    });
}

function buildGrid() {
    const grid = document.getElementById('pt-grid');
    grid.innerHTML = '';

    // Periods 1-7
    for (let period = 1; period <= 7; period++) {
        for (let group = 1; group <= 18; group++) {
            const idx = layout[period] && layout[period][group] !== undefined ? layout[period][group] : null;
            if (idx !== null) {
                grid.appendChild(makeCell(ELEMENTS[idx]));
            } else {
                // Lanthanide/Actinide placeholder
                if ((period === 6 && group === 3) || (period === 7 && group === 3)) {
                    const cls = period === 6 ? 'cat-lanthanide' : 'cat-actinide';
                    const label = period === 6 ? '57-71' : '89-103';
                    const div = document.createElement('div');
                    div.className = `pt-cell ${cls}`;
                    div.style.fontSize = '0.6em';
                    div.style.fontWeight = '600';
                    div.style.color = 'rgba(0,0,0,0.5)';
                    div.textContent = label;
                    grid.appendChild(div);
                } else {
                    const div = document.createElement('div');
                    div.className = 'pt-spacer';
                    grid.appendChild(div);
                }
            }
        }
    }

    // Gap row
    for (let i = 0; i < 18; i++) {
        const div = document.createElement('div'); div.className = 'pt-spacer'; grid.appendChild(div);
    }

    // Lanthanide row (starts at col 3, 15 elements)
    for (let i = 0; i < 2; i++) { const d = document.createElement('div'); d.className='pt-spacer'; grid.appendChild(d); }
    lanthanides.forEach(el => grid.appendChild(makeCell(el)));
    for (let i = 0; i < 1; i++) { const d = document.createElement('div'); d.className='pt-spacer'; grid.appendChild(d); }

    // Actinide row
    for (let i = 0; i < 2; i++) { const d = document.createElement('div'); d.className='pt-spacer'; grid.appendChild(d); }
    actinides.forEach(el => grid.appendChild(makeCell(el)));
    for (let i = 0; i < 1; i++) { const d = document.createElement('div'); d.className='pt-spacer'; grid.appendChild(d); }
}

function makeCell(el) {
    const [num, sym, name, mass, cat] = el;
    const cls = CATEGORIES[cat] ? CATEGORIES[cat].cls : 'cat-unknown';
    const div = document.createElement('div');
    div.className = `pt-cell ${cls}`;
    div.dataset.num = num;
    div.dataset.sym = sym.toLowerCase();
    div.dataset.name = name.toLowerCase();
    div.innerHTML = `<span class="pt-num">${num}</span><span class="pt-sym">${sym}</span><span class="pt-name">${name}</span><span class="pt-mass">${mass}</span>`;
    div.addEventListener('click', () => openDetail(el));
    return div;
}

function openDetail(el) {
    const [num, sym, name, mass, cat, period, group, state, config] = el;
    const cls = CATEGORIES[cat] ? CATEGORIES[cat].cls : 'cat-unknown';
    document.getElementById('dd-sym-box').textContent = sym;
    document.getElementById('dd-sym-box').className = `pt-detail-symbol ${cls}`;
    document.getElementById('dd-name').textContent = name;
    document.getElementById('dd-cat').textContent = CATEGORIES[cat] ? CATEGORIES[cat].label : cat;
    document.getElementById('dd-num').textContent = num;
    document.getElementById('dd-sym').textContent = sym;
    document.getElementById('dd-mass').textContent = mass + ' u';
    document.getElementById('dd-period').textContent = period;
    document.getElementById('dd-group').textContent = group || '—';
    document.getElementById('dd-state').textContent = state;
    document.getElementById('dd-config').textContent = config;
    document.getElementById('pt-detail').classList.add('visible');
    document.getElementById('pt-overlay').classList.add('visible');
}

function closeDetail() {
    document.getElementById('pt-detail').classList.remove('visible');
    document.getElementById('pt-overlay').classList.remove('visible');
}

function filterTable() {
    const q = document.getElementById('pt-search').value.toLowerCase().trim();
    document.querySelectorAll('.pt-cell').forEach(cell => {
        if (!q) { cell.classList.remove('dimmed'); return; }
        const match = cell.dataset.name && (
            cell.dataset.name.includes(q) ||
            cell.dataset.sym.includes(q) ||
            cell.dataset.num === q
        );
        cell.classList.toggle('dimmed', !match);
    });
}

buildLegend();
buildGrid();
</script>
</body>
</html>
