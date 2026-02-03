</div>
<footer class="footer">
    <div class="footer-content">
        <div style="display: flex; align-items: center; justify-content: center; gap: 10px; margin-bottom: 15px;">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="white" stroke="none">
                <path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z" />
            </svg>
            <h2>TERIMA KASIH</h2>
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="white" stroke="none">
                <path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z" />
            </svg>
        </div>
        <p>Atas partisipasi Anda dalam mengisi kuesioner ini</p>

        <div class="footer-contact">
            <h3>Saluran Keluhan & Saran</h3>
            <div class="contact-cards">
                <div class="contact-card">
                    <h3 style="font-size: 16px; font-weight: bold; margin-bottom: 5px; display: flex; align-items: center; justify-content: center; gap: 8px;">
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z" />
                            <polyline points="22 6 12 13 2 6" />
                        </svg>
                        Kotak Saran
                    </h3>
                    <p>Tersedia di lobi rumah sakit</p>
                </div>
                <div class="contact-card">
                    <h3 style="font-size: 16px; font-weight: bold; margin-bottom: 5px; display: flex; align-items: center; justify-content: center; gap: 8px;">
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z" />
                        </svg>
                        Humas
                    </h3>
                    <p>Hubungi bagian Humas kami</p>
                </div>
                <div class="contact-card">
                    <h3 style="font-size: 16px; font-weight: bold; margin-bottom: 5px; display: flex; align-items: center; justify-content: center; gap: 8px;">
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z" />
                        </svg>
                        WA Pengaduan
                    </h3>
                    <p>082244125457</p>
                </div>
            </div>
        </div>

        <div style="margin-top: 20px; font-size: 14px; color: rgba(255,255,255,0.8);">
            <p>Rumah Sakit Eka Husada - Melayani Dengan Sepenuh Hati</p>
        </div>
    </div>
</footer>

<script>
    const bpjsRadio = document.getElementById('bpjs');
    const umumRadio = document.getElementById('umum');
    const q4Section = document.getElementById('question-q4');
    const q4Radios = document.querySelectorAll('input[name="q4"]');
    const q4Bpjs = document.getElementById('q4-bpjs');
    const SERVICE_LOCKED = <?php echo $service_locked ? 'true' : 'false'; ?>;

    if (!SERVICE_LOCKED) {
        document.querySelectorAll('input[name="service"]').forEach(radio => {
            radio.addEventListener('change', () => {
                const val = radio.value;
                const url = new URL(window.location.href);
                url.searchParams.set('service', val);
                // arahkan ke URL baru
                window.location.href = url.toString();
            });
        });
    }

    function disableQ4() {
        if (!q4Section) return;
        q4Section.style.opacity = '0.5';
        q4Radios.forEach(r => {
            r.checked = false;
            r.disabled = true;
        });
        q4Bpjs.value = '0';
    }

    function enableQ4() {
        if (!q4Section) return;
        q4Section.style.opacity = '1';
        q4Radios.forEach(r => {
            r.disabled = false;
        });
        q4Bpjs.value = '';
    }

    bpjsRadio?.addEventListener('change', disableQ4);
    umumRadio?.addEventListener('change', enableQ4);

    // optional: kalau user reload dalam kondisi BPJS sudah kepilih, langsung disable q4
    if (bpjsRadio && bpjsRadio.checked) disableQ4();
</script>

</body>

</html>