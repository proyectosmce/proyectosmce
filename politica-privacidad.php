<?php require_once 'includes/config.php'; ?>
<?php
$lang = $_GET['lang'] ?? '';
$allowed = ['es','en','fr','de','pt','it'];
if (!in_array($lang, $allowed, true)) { $lang = 'es'; }
$t = [
  'es' => [
    'title' => 'Politica de Privacidad · Proyectos MCE',
    'subtitle' => 'Tratamiento de datos para desarrollo de software a medida (Colombia)',
    'sections' => [
      ['Responsable del tratamiento', 'Proyectos MCE (Marlon Carabali) es responsable segun Ley 1581 de 2012 y Decreto 1377 de 2013. Contacto: <a class="text-blue-600 underline" href="mailto:contacto@proyectosmce.com">contacto@proyectosmce.com</a>.'],
      ['Datos que recopilamos', 'Nombre, email, telefono, empresa/proyecto, mensajes enviados, metadatos tecnicos (IP, navegador) y archivos/enlaces que compartas en formularios o comunicaciones.'],
      ['Finalidades y base legal', 'Responder solicitudes, enviar propuestas, ejecutar o preparar contratos, soporte y mejora de servicios. Base legal: consentimiento, ejecucion contractual o interes legitimo cuando corresponda.'],
      ['Conservacion', 'Conservamos los datos mientras exista relacion comercial o sean necesarios para las finalidades descritas y por los plazos legales para obligaciones o reclamaciones.'],
      ['Derechos del titular', 'Acceso, rectificacion, cancelacion, oposicion, revocacion del consentimiento y portabilidad cuando aplique. Solicitalos al correo del responsable; la SIC es la autoridad de vigilancia en Colombia.'],
      ['Seguridad', 'Medidas tecnicas y organizativas razonables (accesos restringidos, cifrado en transito, copias de seguridad). Ningun sistema es 100% seguro.'],
      ['Menores de edad', 'Servicios dirigidos a empresas/adultos. No recopilamos intencionalmente datos de menores; si se detectan, se eliminaran.'],
      ['Encargados y transferencias', 'Podemos compartir datos con proveedores (hosting, email, analitica) bajo contratos de encargo y confidencialidad. No vendemos datos personales.'],
      ['Modificaciones', 'La version vigente es la publicada con su fecha de actualizacion. Notificaremos cambios sustanciales por este medio.'],
    ],
    'back_home' => 'Volver al inicio',
    'contact' => 'Contactar'
  ],
  'en' => [
    'title' => 'Privacy Policy · MCE Projects',
    'subtitle' => 'Data processing for custom software development (Colombia)',
    'sections' => [
      ['Data Controller', 'MCE Projects (Marlon Carabali) is the controller under Colombian Law 1581/2012. Email: <a class="text-blue-600 underline" href="mailto:contacto@proyectosmce.com">contacto@proyectosmce.com</a>.'],
      ['Data we collect', 'Name, email, phone, company/project, messages sent, technical metadata (IP, browser) and files/links shared via forms.'],
      ['Purpose and legal basis', 'Respond to inquiries, send proposals, perform or prepare contracts, provide support and improve services. Legal basis: consent, contract performance or legitimate interest when appropriate.'],
      ['Retention', 'Data kept while the business relationship exists or as needed for the stated purposes and legal retention terms.'],
      ['Rights', 'Access, rectification, erasure, objection, withdrawal of consent and portability where applicable. Submit requests to the controller email; the SIC is the supervisory authority in Colombia.'],
      ['Security', 'Reasonable technical/organizational measures (restricted access, in-transit encryption, backups). No system is 100% secure.'],
      ['Minors', 'Services aimed at businesses/adults. We do not knowingly collect data from minors; if detected, it will be deleted.'],
      ['Processors and transfers', 'We may share data with providers (hosting, email, analytics) under processor contracts and confidentiality. No data is sold.'],
      ['Changes', 'The current version is the one published with its update date. Material changes will be announced here.'],
    ],
    'back_home' => 'Back to home',
    'contact' => 'Contact'
  ],
  'fr' => [
    'title' => 'Politique de confidentialite · Projets MCE',
    'subtitle' => 'Traitement de donnees pour le developpement logiciel sur mesure (Colombie)',
    'sections' => [
      ['Responsable du traitement', 'Projets MCE (Marlon Carabali) est responsable selon la Loi 1581/2012. Email : <a class="text-blue-600 underline" href="mailto:contacto@proyectosmce.com">contacto@proyectosmce.com</a>.'],
      ['Donnees collectees', 'Nom, email, telephone, entreprise/projet, messages, metadonnees techniques (IP, navigateur) et fichiers/liens fournis.'],
      ['Finalites et base legale', 'Repondre aux demandes, envoyer des propositions, executer ou preparer des contrats, support et amelioration. Base: consentement, contrat ou interet legitime.'],
      ['Conservation', 'Pendant la relation ou selon les delais legaux necessaires aux finalites et obligations.'],
      ['Droits', 'Acces, rectification, suppression, opposition, retrait du consentement et portabilite si applicable. Adressez-vous au responsable; la SIC est l’autorite de controle en Colombie.'],
      ['Securite', 'Mesures techniques/organisationnelles raisonnables (acces restreint, chiffrement en transit, sauvegardes). Aucun systeme n’est 100% sur.'],
      ['Mineurs', 'Services destines aux entreprises/adultes. Pas de collecte volontaire de donnees de mineurs; si detectees, elles seront supprimees.'],
      ['Sous-traitants et transferts', 'Partage possible avec prestataires (hebergement, email, analytics) sous clauses de confidentialite. Aucune vente de donnees.'],
      ['Modifications', 'La version en vigueur est celle publiee avec sa date. Les changements importants seront annonces ici.'],
    ],
    'back_home' => 'Retour a l’accueil',
    'contact' => 'Contact'
  ],
  'de' => [
    'title' => 'Datenschutzerklarung · MCE Projekte',
    'subtitle' => 'Datenverarbeitung fur individuelle Softwareentwicklung (Kolumbien)',
    'sections' => [
      ['Verantwortlicher', 'MCE Projekte (Marlon Carabali) ist Verantwortlicher nach kolumbianischem Recht 1581/2012. E-Mail: <a class="text-blue-600 underline" href="mailto:contacto@proyectosmce.com">contacto@proyectosmce.com</a>.'],
      ['Welche Daten wir erfassen', 'Name, E-Mail, Telefon, Firma/Projekt, Nachrichten, technische Metadaten (IP, Browser) sowie Dateien/Links aus Formularen.'],
      ['Zweck und Rechtsgrundlage', 'Anfragen beantworten, Angebote senden, Vertrage erfullen/vorbereiten, Support und Serviceverbesserung. Rechtsgrundlage: Einwilligung, Vertrag oder berechtigtes Interesse.'],
      ['Speicherdauer', 'Solange die Geschaftsbeziehung besteht oder gesetzliche Pflichten gelten.'],
      ['Rechte', 'Auskunft, Berichtigung, Loschung, Widerspruch, Widerruf der Einwilligung und Datenubertragbarkeit, soweit anwendbar. Anfragen an die genannte E-Mail; Aufsicht: SIC (Kolumbien).'],
      ['Sicherheit', 'Angemessene technische/organisatorische Massnahmen (Zugangsbeschrankung, Verschlusselung in Transit, Backups). Kein System ist 100% sicher.'],
      ['Minderjahrige', 'Services fur Unternehmen/Erwachsene. Keine vorsatzliche Erhebung von Daten Minderjahriger; werden sie erkannt, loschen wir sie.'],
      ['Auftragsverarbeiter/Weitergaben', 'Weitergabe an Dienstleister (Hosting, E-Mail, Analytics) mit Vertraulichkeit. Keine Datenverkaufe.'],
      ['Anderungen', 'Maßgeblich ist die veroffentlichte Version mit Datum; wesentliche Anpassungen werden hier bekanntgegeben.'],
    ],
    'back_home' => 'Zur Startseite',
    'contact' => 'Kontakt'
  ],
  'pt' => [
    'title' => 'Politica de Privacidade · Projetos MCE',
    'subtitle' => 'Tratamento de dados para desenvolvimento sob medida (Colombia)',
    'sections' => [
      ['Responsavel', 'Projetos MCE (Marlon Carabali) e o controlador conforme a Lei 1581/2012. Email: <a class="text-blue-600 underline" href="mailto:contacto@proyectosmce.com">contacto@proyectosmce.com</a>.'],
      ['Dados coletados', 'Nome, email, telefone, empresa/projeto, mensagens, metadados tecnicos (IP, navegador) e arquivos/links enviados.'],
      ['Finalidade e base legal', 'Responder, enviar propostas, executar/preparar contratos, suporte e melhoria. Base: consentimento, execucao contratual ou interesse legitimo.'],
      ['Conservacao', 'Durante a relacao comercial ou prazos legais necessarios.'],
      ['Direitos', 'Acesso, retificacao, cancelamento, oposicao, revogacao do consentimento e portabilidade quando aplicavel. Solicite por email; autoridade supervisora: SIC (Colombia).'],
      ['Seguranca', 'Medidas tecnicas/organizacionais razoaveis (acesso restrito, criptografia em transito, backups). Nenhum sistema e 100% seguro.'],
      ['Menores', 'Servicos para empresas/adultos. Nao coletamos dados de menores intencionalmente; se detectados, serao excluidos.'],
      ['Encarregados e transferencias', 'Compartilhamento possivel com provedores (hosting, email, analytics) sob confidencialidade. Nao vendemos dados.'],
      ['Modificacoes', 'Versao vigente e a publicada com data; alteracoes relevantes serao comunicadas aqui.'],
    ],
    'back_home' => 'Voltar ao inicio',
    'contact' => 'Contato'
  ],
  'it' => [
    'title' => 'Informativa sulla privacy · Progetti MCE',
    'subtitle' => 'Trattamento dati per sviluppo software su misura (Colombia)',
    'sections' => [
      ['Titolare', 'Progetti MCE (Marlon Carabali) e il titolare secondo la Legge colombiana 1581/2012. Email: <a class="text-blue-600 underline" href="mailto:contacto@proyectosmce.com">contacto@proyectosmce.com</a>.'],
      ['Dati raccolti', 'Nome, email, telefono, azienda/progetto, messaggi, metadati tecnici (IP, browser) e file/link inviati.'],
      ['Finalita e base giuridica', 'Rispondere, inviare proposte, eseguire o preparare contratti, supporto e miglioramento. Base: consenso, esecuzione contrattuale o interesse legittimo.'],
      ['Conservazione', 'Per la durata del rapporto o i termini legali necessari.'],
      ['Diritti', 'Accesso, rettifica, cancellazione, opposizione, revoca del consenso e portabilita quando applicabile. Richieste via email; autorita: SIC (Colombia).'],
      ['Sicurezza', 'Misure tecniche/organizzative ragionevoli (accesso limitato, cifratura in transito, backup). Nessun sistema e 100% sicuro.'],
      ['Minori', 'Servizi per aziende/adulti. Non raccogliamo dati di minori; se rilevati, li elimineremo.'],
      ['Responsabili e trasferimenti', 'Possibile condivisione con fornitori (hosting, email, analytics) con obbligo di riservatezza. Non vendiamo dati.'],
      ['Modifiche', 'Fa fede la versione pubblicata con data; modifiche rilevanti saranno comunicate qui.'],
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

<!-- Página de privacidad sin footer para foco en el contenido -->
</body>
</html>
