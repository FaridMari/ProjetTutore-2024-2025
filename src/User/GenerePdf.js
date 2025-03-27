const puppeteer = require('puppeteer');
const path = require('path');

const sessionId = process.argv[2];  // PHPSESSID
const ficheUrl = process.argv[3];   // ex: src/Enseignant/FicheContrainte.php
const outputPath = process.argv[4]; // ex: fiche_voeux.pdf

console.log('Session ID :', sessionId);
console.log('Fiche URL :', ficheUrl);
console.log('Chemin de sortie du PDF :', outputPath);

(async () => {
    let browser;
    try {
        browser = await puppeteer.launch({ headless: true });
        const page = await browser.newPage();

        await page.setCookie({
            name: 'PHPSESSID',
            value: sessionId,
            domain: 'localhost',
            path: '/',
            httpOnly: true,
        });

        console.log('Cookies injectés :', await page.cookies());

        const fullUrl = `http://localhost/tutore6/ProjetTutore-2024-2025/${ficheUrl}?pdf=1`;
        console.log('Chargement de l’URL :', fullUrl);

        await page.goto(fullUrl, { waitUntil: 'networkidle2', timeout: 60000 });

        console.log('Page chargée, attente de contenu dynamique...');
        await new Promise(resolve => setTimeout(resolve, 2000));

        await page.pdf({
            path: path.resolve(outputPath),
            format: 'A4',
            printBackground: true,
            margin: { top: '20mm', right: '15mm', bottom: '20mm', left: '15mm' }
        });

        console.log('✅ PDF généré avec succès à :', path.resolve(outputPath));

    } catch (error) {
        console.error('❌ Erreur lors de la génération du PDF :', error);
    } finally {
        if (browser) await browser.close();
    }
})();
