<?php

$keys = [
    "Verify Your Identity" => [
        "en" => "Verify Your Identity",
        "ar" => "تحقق من هويتك",
        "hi" => "अपनी पहचान सत्यापित करें",
        "ml" => "നിങ്ങളുടെ ഐഡന്റിറ്റി സ്ഥിരീകരിക്കുക",
        "de" => "Identität bestätigen",
        "fr" => "Vérifiez votre identité"
    ],
    "Secure Login Verification" => [
        "en" => "Secure Login Verification",
        "ar" => "التحقق الآمن من تسجيل الدخول",
        "hi" => "सुरक्षित लॉगिन सत्यापन",
        "ml" => "സുരക്ഷിത ലോഗിൻ സ്ഥിരീകരണം",
        "de" => "Sichere Login-Verifizierung",
        "fr" => "Vérification sécurisée de la connexion"
    ],
    "Enter Verification Code" => [
        "en" => "Enter Verification Code",
        "ar" => "أدخل رمز التحقق",
        "hi" => "सत्यापन कोड दर्ज करें",
        "ml" => "സ്ഥിരീകരണ കോഡ് നൽകുക",
        "de" => "Bestätigungscode eingeben",
        "fr" => "Entrez le code de vérification"
    ],
    "Verify Code" => [
        "en" => "Verify Code",
        "ar" => "تأكيد الرمز",
        "hi" => "कोड सत्यापित करें",
        "ml" => "കോഡ് സ്ഥിരീകരിക്കുക",
        "de" => "Code bestätigen",
        "fr" => "Vérifier le code"
    ],
    "Resend Code" => [
        "en" => "Resend Code",
        "ar" => "إعادة إرسال الرمز",
        "hi" => "कोड पुनः भेजें",
        "ml" => "കോഡ് വീണ്ടും അയക്കുക",
        "de" => "Code erneut senden",
        "fr" => "Renvoyer le code"
    ],
    "Code expires in" => [
        "en" => "Code expires in",
        "ar" => "تنتهي صلاحية الرمز خلال",
        "hi" => "कोड समाप्त होता है",
        "ml" => "കോഡ് കാലഹരണപ്പെടുന്നത്",
        "de" => "Code läuft ab in",
        "fr" => "Le code expire dans"
    ],
    "Back to Login" => [
        "en" => "Back to Login",
        "ar" => "العودة إلى تسجيل الدخول",
        "hi" => "लॉगिन पर वापस जाएं",
        "ml" => "തിരികെ ലോഗിനിലേക്ക്",
        "de" => "Zurück zum Login",
        "fr" => "Retour à la connexion"
    ],
    "Account Recovery" => [
        "en" => "Account Recovery",
        "ar" => "استرداد الحساب",
        "hi" => "खाता पुनर्प्राप्ति",
        "ml" => "അക്കൗണ്ട് വീണ്ടെടുക്കൽ",
        "de" => "Kontowiederherstellung",
        "fr" => "Récupération de compte"
    ],
    "Set New Password" => [
        "en" => "Set New Password",
        "ar" => "تعيين كلمة مرور جديدة",
        "hi" => "नया पासवर्ड सेट करें",
        "ml" => "പുതിയ പാസ്‌വേഡ് സജ്ജമാക്കുക",
        "de" => "Neues Passwort festlegen",
        "fr" => "Définir un nouveau mot de passe"
    ],
    "Reset Password" => [
        "en" => "Reset Password",
        "ar" => "إعادة تعيين كلمة المرور",
        "hi" => "पासवर्ड रीसेट करें",
        "ml" => "പാസ്‌വേഡ് പുനഃസജ്ജമാക്കുക",
        "de" => "Passwort zurücksetzen",
        "fr" => "Réinitialiser le mot de passe"
    ],
    "Send Reset Code" => [
        "en" => "Send Reset Code",
        "ar" => "إرسال رمز إعادة التعيين",
        "hi" => "रीसेट कोड भेजें",
        "ml" => "റീസെറ്റ് കോഡ് അയക്കുക",
        "de" => "Reset-Code senden",
        "fr" => "Envoyer le code de réinitialisation"
    ]
];

$locales = ['en', 'ar', 'hi', 'ml', 'de', 'fr'];

foreach ($locales as $loc) {
    $path = __DIR__ . "/../lang/{$loc}.json";
    if (!file_exists($path)) continue;
    $content = json_decode(file_get_contents($path), true) ?: [];
    foreach ($keys as $k => $trans) {
        $content[$k] = $trans[$loc] ?? $trans['en'];
    }
    ksort($content);
    file_put_contents($path, json_encode($content, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
    echo "Updated {$loc}.json\n";
}
