@php
  $siteUrl = rtrim(config('app.url') ?: url('/'), '/');
  $seo = [
    'html_lang' => 'en',
    'title' => 'Terms of Responsibility | IA Tio Ben',
    'description' => 'Terms of responsibility for IA Tio Ben: nature of the service, limitations, and guidance for responsible use.',
    'canonical' => $siteUrl . '/en/terms-of-responsibility',
    'og_title' => 'Terms of Responsibility | IA Tio Ben',
    'og_description' => 'Nature of the service, limitations, and guidance for responsible use.',
    'og_locale' => 'en_US',
    'og_image' => $siteUrl . '/og?title=IA%20Tio%20Ben&description=Terms%20of%20Responsibility',
  ];
@endphp

@extends('layouts.app') {{-- ou layouts.site, se preferir unificar --}}
@section('title', $seo['title'])
@section('meta_description', $seo['description'])
@section('canonical', $seo['canonical'])
@section('og_title', $seo['og_title'])
@section('og_description', $seo['og_description'])
@section('og_url', $seo['canonical'])
@section('og_locale', $seo['og_locale'])
@section('og_image', $seo['og_image'])

@section('content')
<section class="max-w-6xl mx-auto px-4 py-12">
  <h1 class="text-3xl md:text-4xl font-bold text-center text-amber-900 mb-10">
    Terms of Responsibility
  </h1>

  <div class="border border-amber-300 bg-amber-50 shadow-lg rounded-2xl">
    <div class="p-6 space-y-4">
      <p class="text-amber-900">
        <strong>Website:</strong> iatioben.com.br <br />
        <strong>Last updated:</strong> August 2025
      </p>

      <h2 class="text-2xl font-semibold text-amber-800">1. Nature of the Service</h2>
      <p class="text-amber-900">
        iatioben.com.br is an AI-based spiritual guidance service that provides answers grounded exclusively in official Catholic teaching, including Sacred Scripture (Catholic Bible), the Catechism of the Catholic Church, papal documents, and Apostolic Tradition.
      </p>
      <p class="text-amber-900 font-semibold">
        IMPORTANT: This service does not replace in-person guidance from a priest, qualified catechist, spiritual director, or healthcare professionals. The answers are strictly informational and educational regarding the Catholic faith.
      </p>

      <h2 class="text-2xl font-semibold text-amber-800">2. Service Limitations</h2>
      <h3 class="text-xl font-semibold text-amber-800">2.1 Technological Nature</h3>
      <ul class="list-disc list-inside text-amber-900 space-y-1">
        <li>“Tio Ben” is an artificial intelligence system and may present limitations or interpretative errors</li>
        <li>Answers are generated automatically based on programming and doctrinal sources</li>
        <li>For complex questions or apparent contradictions, consult a priest or in-person catechist</li>
      </ul>

      <h3 class="text-xl font-semibold text-amber-800">2.2 Scope of Guidance</h3>
      <ul class="list-disc list-inside text-amber-900 space-y-1">
        <li>Answers do not constitute professional medical, psychological, legal, or financial advice</li>
        <li>Personal, sacramental, or individualized matters should be directed to a priest</li>
        <li>The service does not provide emergency support or handle imminent-risk situations</li>
      </ul>

      <h2 class="text-2xl font-semibold text-amber-800">3. Situations Requiring Professional Help</h2>
      <h3 class="text-xl font-semibold text-amber-800">3.1 Emergencies</h3>
      <p class="text-amber-900">In cases of:</p>
      <ul class="list-disc list-inside text-amber-900 space-y-1">
        <li>Suicidal thoughts or self-harm</li>
        <li>Domestic violence or abuse</li>
        <li>Severe psychiatric or psychological crises</li>
        <li>Situations involving risk to life or safety</li>
      </ul>

      <p class="text-amber-900 font-semibold">SEEK IMMEDIATE HELP:</p>
      <ul class="list-disc list-inside text-amber-900 space-y-1">
        <li>If you are in Brazil: CVV (Emotional Support): 188</li>
        <li>In Brazil: SAMU (Emergency Medical Services): 192</li>
        <li>In Brazil: Police: 190</li>
        <li>In Brazil: Fire Department: 193</li>
        <li>In Brazil: Human Rights Hotline: 100</li>
        <li>Outside Brazil: contact your local emergency number immediately</li>
      </ul>

      <h3 class="text-xl font-semibold text-amber-800">3.2 Specialized Support</h3>
      <p class="text-amber-900">
        For mental health concerns, complex relationships, addictions, complicated grief, or trauma, seek:
      </p>
      <ul class="list-disc list-inside text-amber-900 space-y-1">
        <li>Mental health professionals (psychologists, psychiatrists)</li>
        <li>Your parish priest or spiritual director</li>
        <li>Catechists and pastoral agents in your community</li>
        <li>Specialized support groups</li>
      </ul>

      <h2 class="text-2xl font-semibold text-amber-800">4. Responsible Use</h2>
      <h3 class="text-xl font-semibold text-amber-800">4.1 User Commitment</h3>
      <p class="text-amber-900">By using this service, you declare that you:</p>
      <ul class="list-disc list-inside text-amber-900 space-y-1">
        <li>Are 18+ years old or under a legal guardian’s supervision</li>
        <li>Understand the limitations of an automated service</li>
        <li>Will not use answers as the sole basis for important decisions</li>
        <li>Will seek confirmation in official Church sources when needed</li>
      </ul>

      <h3 class="text-xl font-semibold text-amber-800">4.2 Question Content</h3>
      <ul class="list-disc list-inside text-amber-900 space-y-1">
        <li>Maintain respect and Christian dignity in interactions</li>
        <li>Avoid offensive or blasphemous content</li>
        <li>Do not share sensitive personal information unnecessarily</li>
        <li>This is not a space for sacramental confession</li>
      </ul>

      <h2 class="text-2xl font-semibold text-amber-800">5. Privacy and Data</h2>
      <h3 class="text-xl font-semibold text-amber-800">5.1 Data Collection</h3>
      <ul class="list-disc list-inside text-amber-900 space-y-1">
        <li><strong>We do NOT collect personal data</strong> that identifies users</li>
        <li><strong>We do NOT store</strong> data such as name, email, phone number, or address</li>
        <li><strong>We cannot identify</strong> who asked a specific question</li>
        <li><strong>We do NOT</strong> perform individual user tracking</li>
      </ul>

      <h3 class="text-xl font-semibold text-amber-800">5.2 Stored Information</h3>
      <ul class="list-disc list-inside text-amber-900 space-y-1">
        <li>We store exclusively:</li>
        <li>The content of questions asked to the system</li>
        <li>Answers provided by “Tio Ben”</li>
        <li>Date and time of interactions</li>
        <li>Non-identifiable technical data (browser, operating system)</li>
      </ul>

      <h3 class="text-xl font-semibold text-amber-800">5.3 Purpose</h3>
      <ul class="list-disc list-inside text-amber-900 space-y-1">
        <li>Stored data is used only for:</li>
        <li>Improving answer quality</li>
        <li>Statistical analysis of functionality</li>
        <li>Technical maintenance</li>
        <li>Non-identifiable studies of common Catholic faith questions</li>
      </ul>

      <h3 class="text-xl font-semibold text-amber-800">5.4 Sharing</h3>
      <ul class="list-disc list-inside text-amber-900 space-y-1">
        <li>We NEVER share user data with third parties for commercial purposes</li>
        <li>We NEVER sell or transfer user information</li>
        <li>Data may be accessed only by the technical team responsible for the system</li>
      </ul>

      <h2 class="text-2xl font-semibold text-amber-800">6. Doctrinal Foundation</h2>
      <h3 class="text-xl font-semibold text-amber-800">6.1 Official Sources</h3>
      <ul class="list-disc list-inside text-amber-900 space-y-1">
        <li>Our answers are grounded exclusively in:</li>
        <li>Sacred Scripture (Catholic Bible)</li>
        <li>Catechism of the Catholic Church</li>
        <li>Ecumenical Councils’ documents</li>
        <li>Encyclicals, exhortations, and papal documents</li>
        <li>Apostolic Tradition recognized by the Church</li>
      </ul>

      <h3 class="text-xl font-semibold text-amber-800">6.2 Magisterial Authority</h3>
      <ul class="list-disc list-inside text-amber-900 space-y-1">
        <li>We recognize the supreme authority of the Pope and the bishops in communion with him</li>
        <li>In case of interpretative conflict, official Church Magisterium prevails</li>
        <li>We recommend consulting primary sources and local ecclesial authorities</li>
      </ul>

      <h2 class="text-2xl font-semibold text-amber-800">7. Disclaimer</h2>
      <h3 class="text-xl font-semibold text-amber-800">7.1 Personal Decisions</h3>
      <ul class="list-disc list-inside text-amber-900 space-y-1">
        <li>The user is fully responsible for their decisions and actions</li>
        <li>Answers do not replace personal discernment and prayer</li>
        <li>Important decisions should include qualified in-person guidance</li>
      </ul>

      <h3 class="text-xl font-semibold text-amber-800">7.2 Consequences</h3>
      <ul class="list-disc list-inside text-amber-900 space-y-1">
        <li>iatioben.com.br is not responsible for outcomes of decisions made solely based on the answers provided</li>
        <li>We assume no responsibility for misuse or misinterpretation</li>
        <li>Each user should exercise Christian prudence in discernment</li>
      </ul>

      <h2 class="text-2xl font-semibold text-amber-800">8. Changes to These Terms</h2>
      <ul class="list-disc list-inside text-amber-900 space-y-1">
        <li>These terms may be updated periodically</li>
        <li>Significant changes will be communicated on the home page</li>
        <li>Continued use implies acceptance of the updated terms</li>
      </ul>

      <h2 class="text-2xl font-semibold text-amber-800">9. Contact</h2>
      <p class="text-amber-900">
        For questions about these terms or how the service works, use the contact information available on the website.
      </p>

      <h2 class="text-2xl font-semibold text-amber-800">10. Final Provisions</h2>
      <p class="text-amber-900">
        These terms are governed by Brazilian law. By using iatioben.com.br, you declare that you have read, understood, and fully accepted these conditions.
      </p>

      <p class="text-amber-900 italic">
        “Whatever you do, do everything in the name of the Lord Jesus, giving thanks to God the Father through him.” (Colossians 3:17)
      </p>

      <p class="text-amber-900 mt-6 italic">
        *May Our Lady intercede for everyone seeking to grow in faith through this tool, and may the Holy Spirit guide our hearts into the fullness of truth in Jesus Christ.*
      </p>
    </div>
  </div>
</section>
@endsection
