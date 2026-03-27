<?php require_once 'includes/config.php'; ?>
<?php
$lang = $_GET['lang'] ?? '';
$allowed = ['es','en','fr','de','pt','it'];
if (!in_array($lang, $allowed, true)) { $lang = 'es'; }
$t = [
  'es' => [
    'title' => 'Aviso Legal · Proyectos MCE',
    'subtitle' => 'Desarrollo de software a medida en Colombia',
    'sections' => [
      ['Identificación del titular', 'Proyectos MCE es la marca comercial de Marlon Carabalí, NIT pendiente, con domicilio en Colombia. Correo: <a class="text-blue-600 underline" href="mailto:contacto@proyectosmce.com">contacto@proyectosmce.com</a>.'],
      ['Objeto del sitio', 'Brindar información de servicios de desarrollo de software, automatización y soporte. El uso del sitio implica aceptar este Aviso Legal.'],
      ['Condiciones de uso', 'El usuario se compromete a usar los contenidos de forma lícita, sin vulnerar derechos de terceros ni intentar afectar la seguridad o disponibilidad del sitio.'],
      ['Propiedad intelectual', 'Textos, diseños, código, marcas y logotipos son titularidad de Proyectos MCE o licenciantes. Se prohíbe su reproducción, distribución o transformación sin autorización escrita.'],
      ['Responsabilidad', 'Se procura exactitud y disponibilidad, pero no se garantiza ausencia de errores o interrupciones. Proyectos MCE no responde por daños derivados del uso del sitio ni por decisiones basadas en su contenido.'],
      ['Enlaces externos', 'Los enlaces a terceros se ofrecen como referencia. Proyectos MCE no controla su contenido ni políticas y no asume responsabilidad por ellos.'],
      ['Modificaciones', 'Podemos modificar el contenido del sitio y este Aviso Legal. La versión vigente es la publicada con su fecha de actualización.'],
      ['Ley y jurisdicción', 'Se rige por las leyes de la República de Colombia. Controversias: jueces competentes en Colombia.'],
    ],
    'back_home' => 'Volver al inicio',
    'contact' => 'Contactar'
  ],
  'en' => [
    'title' => 'Legal Notice · MCE Projects',
    'subtitle' => 'Custom software development in Colombia',
    'sections' => [
      ['Owner Identification', 'MCE Projects is the trade name of Marlon Carabalí, based in Colombia. Contact: <a class="text-blue-600 underline" href="mailto:contacto@proyectosmce.com">contacto@proyectosmce.com</a>.'],
      ['Terms of Use', 'Access and use of this site imply acceptance of this Legal Notice. Users agree to lawful, diligent, good-faith use of the content and services.'],
      ['Intellectual Property', 'All content (text, design, code, trademarks and logos) belongs to MCE Projects or its licensors. Reproduction, distribution or transformation is forbidden without prior written consent.'],
      ['Liability Limitation', 'We strive for accurate information and availability but do not guarantee freedom from errors or interruptions. MCE Projects is not liable for damages arising from use of the site or decisions based on its content.'],
      ['External Links', 'This site may include links to third parties. MCE Projects does not control or endorse their content or policies. Access is at your own risk.'],
      ['Changes', 'We may modify this Legal Notice and site content. Changes take effect upon publication; please review regularly.'],
      ['Governing Law', 'This Legal Notice is governed by the laws of Colombia. Any dispute will be submitted to the competent courts in Colombia.'],
    ],
    'back_home' => 'Back to home',
    'contact' => 'Contact'
  ],
  'fr' => [
    'title' => 'Mentions légales · Projets MCE',
    'subtitle' => 'Développement logiciel sur mesure en Colombie',
    'sections' => [
      ['Identification du titulaire', 'Projets MCE est la marque commerciale de Marlon Carabalí, basé en Colombie. Contact : <a class="text-blue-600 underline" href="mailto:contacto@proyectosmce.com">contacto@proyectosmce.com</a>.'],
      ['Conditions d’utilisation', "L’accès et l’usage du site impliquent l’acceptation des présentes mentions légales. L’utilisateur s’engage à un usage licite, diligent et de bonne foi des contenus et services."],
      ['Propriété intellectuelle', 'Tous les contenus (textes, design, code, marques et logos) appartiennent à Projets MCE ou à ses concédants. Toute reproduction, distribution ou transformation est interdite sans autorisation écrite.'],
      ['Limitation de responsabilité', 'Bien que nous visons l’exactitude et la disponibilité, nous ne garantissons pas l’absence d’erreurs ou d’interruptions. Projets MCE ne sera pas responsable des dommages résultant de l’usage du site.'],
      ['Liens externes', 'Le site peut contenir des liens vers des tiers. Projets MCE ne contrôle ni ne répond de leur contenu ou politiques. L’accès se fait sous votre responsabilité.'],
      ['Modifications', 'Nous pouvons modifier ces mentions légales et le contenu du site. Les changements sont effectifs dès leur publication. Consultez régulièrement.'],
      ['Droit applicable', 'Ces mentions légales sont régies par le droit colombien. Les litiges seront soumis aux tribunaux compétents en Colombie.'],
    ],
    'back_home' => 'Retour à l’accueil',
    'contact' => 'Contact'
  ],
  'de' => [
    'title' => 'Rechtlicher Hinweis · MCE Projekte',
    'subtitle' => 'Individuelle Softwareentwicklung in Kolumbien',
    'sections' => [
      ['Anbieterkennzeichnung', 'MCE Projekte ist die Handelsmarke von Marlon Carabalí mit Sitz in Kolumbien. Kontakt: <a class="text-blue-600 underline" href="mailto:contacto@proyectosmce.com">contacto@proyectosmce.com</a>.'],
      ['Nutzungsbedingungen', 'Zugriff und Nutzung bedeuten die Zustimmung zu diesem Hinweis. Der Nutzer verpflichtet sich zu rechtmäßiger, sorgfältiger und redlicher Nutzung der Inhalte und Dienste.'],
      ['Urheberrecht', 'Alle Inhalte (Texte, Design, Code, Marken, Logos) gehören MCE Projekte oder Lizenzgebern. Vervielfältigung, Verbreitung oder Veränderung ist ohne schriftliche Zustimmung untersagt.'],
      ['Haftungsbeschränkung', 'Wir bemühen uns um Genauigkeit und Verfügbarkeit, garantieren aber keine Fehlerfreiheit oder Unterbrechungsfreiheit. MCE Projekte haftet nicht für Schäden aus der Nutzung der Seite.'],
      ['Externe Links', 'Diese Seite kann Links zu Dritten enthalten. MCE Projekte kontrolliert deren Inhalte/Politiken nicht; Zugriff erfolgt auf eigenes Risiko.'],
      ['Änderungen', 'Wir können diesen Hinweis und Inhalte ändern; Änderungen gelten ab Veröffentlichung. Regelmäßig prüfen.'],
      ['Anwendbares Recht', 'Es gilt das Recht der Republik Kolumbien. Zuständig sind die Gerichte in Kolumbien.'],
    ],
    'back_home' => 'Zur Startseite',
    'contact' => 'Kontakt'
  ],
  'pt' => [
    'title' => 'Aviso Legal · Projetos MCE',
    'subtitle' => 'Desenvolvimento de software sob medida na Colômbia',
    'sections' => [
      ['Identificação do titular', 'Projetos MCE é a marca de Marlon Carabalí, com sede na Colômbia. Contato: <a class="text-blue-600 underline" href="mailto:contacto@proyectosmce.com">contacto@proyectosmce.com</a>.'],
      ['Condições de uso', 'O acesso e uso do site implicam a aceitação deste aviso. O usuário se compromete a uso lícito, diligente e de boa-fé dos conteúdos e serviços.'],
      ['Propriedade intelectual', 'Todo o conteúdo (textos, design, código, marcas e logotipos) pertence a Projetos MCE ou licenciadores. É proibida reprodução, distribuição ou modificação sem autorização escrita.'],
      ['Limitação de responsabilidade', 'Buscamos precisão e disponibilidade, mas não garantimos ausência de erros ou interrupções. Projetos MCE não se responsabiliza por danos decorrentes do uso do site.'],
      ['Links externos', 'O site pode conter links de terceiros. Projetos MCE não controla nem responde por seu conteúdo ou políticas. O acesso é por sua conta e risco.'],
      ['Modificações', 'Podemos modificar este aviso e o conteúdo do site. As alterações vigoram na publicação; revise periodicamente.'],
      ['Legislação aplicável', 'Aplica-se a lei da República da Colômbia. Controvérsias serão submetidas aos tribunais competentes na Colômbia.'],
    ],
    'back_home' => 'Voltar ao início',
    'contact' => 'Contato'
  ],
  'it' => [
    'title' => 'Note legali · Progetti MCE',
    'subtitle' => 'Sviluppo software su misura in Colombia',
    'sections' => [
      ['Identificazione del titolare', 'Progetti MCE è il marchio di Marlon Carabalí, con sede in Colombia. Contatto: <a class="text-blue-600 underline" href="mailto:contacto@proyectosmce.com">contacto@proyectosmce.com</a>.'],
      ['Condizioni d’uso', "L’accesso e l’uso del sito implicano l’accettazione di queste note. L’utente si impegna a un uso lecito, diligente e in buona fede dei contenuti e servizi."],
      ['Proprietà intellettuale', 'Tutti i contenuti (testi, design, codice, marchi e loghi) appartengono a Progetti MCE o ai licenzianti. È vietata la riproduzione, distribuzione o modifica senza consenso scritto.'],
      ['Limitazione di responsabilità', 'Puntiamo a accuratezza e disponibilità ma non garantiamo assenza di errori o interruzioni. Progetti MCE non è responsabile di danni derivanti dall’uso del sito.'],
      ['Link esterni', 'Il sito può contenere link a terzi. Progetti MCE non controlla né risponde per i loro contenuti o politiche. L’accesso è a proprio rischio.'],
      ['Modifiche', 'Possiamo modificare queste note e il contenuto del sito. Le modifiche sono efficaci dalla pubblicazione; si consiglia di controllare periodicamente.'],
      ['Legge applicabile', 'Si applica la legge della Repubblica di Colombia. Le controversie saranno sottoposte ai tribunali competenti in Colombia.'],
    ],
    'back_home' => 'Torna alla home',
    'contact' => 'Contatto'
  ],
];
$tx = $t[$lang] ?? $t['es'];
?>
<?php require_once 'includes/header.php'; ?>
<script>
  window.mceCurrentLang = '<?php echo $lang; ?>';
  localStorage.setItem('siteLang','<?php echo $lang; ?>');
</script>

<style>
/* Ocultar navegación principal y menú móvil en páginas legales */
.hidden.md\:flex { display: none !important; }
.md\:hidden.flex.items-center.gap-3 { display: none !important; }
#mobile-menu { display: none !important; }
</style>

<main class="bg-slate-100">
  <section class="max-w-5xl mx-auto px-4 py-12">
    <article class="bg-white rounded-2xl shadow-lg border border-slate-200 px-6 md:px-10 py-10 space-y-8">
      <header class="space-y-2">
        <p class="text-sm text-slate-500">Actualizado: <?php echo date('F d, Y'); ?></p>
        <h1 class="text-3xl font-bold" style="color:#1a2c3e;"><?php echo $tx['title']; ?></h1>
        <p class="text-lg font-semibold" style="color:#2c5282;"><?php echo $tx['subtitle']; ?></p>
      </header>

      <?php foreach ($tx['sections'] as $index => $sec): ?>
        <section class="space-y-3">
          <h2 class="text-xl font-semibold" style="color:#1a2c3e;"><?php echo ($index+1).'. '.$sec[0]; ?></h2>
          <p class="text-base leading-relaxed" style="color:#4a5568;"><?php echo $sec[1]; ?></p>
        </section>
      <?php endforeach; ?>

      <footer class="pt-2 flex flex-wrap gap-3">
        <a href="<?php echo app_url(); ?>" class="inline-flex items-center px-4 py-2 rounded-lg border border-slate-200 text-slate-800 font-semibold hover:bg-slate-50 transition"><?php echo $tx['back_home']; ?></a>
        <a href="<?php echo app_url('contacto.php'); ?>" class="inline-flex items-center px-4 py-2 rounded-lg bg-blue-600 text-white font-semibold hover:bg-blue-700 transition"><?php echo $tx['contact']; ?></a>
      </footer>
    </article>
  </section>
</main>

<!-- Página legal sin footer para foco en el contenido -->
</body>
</html>
