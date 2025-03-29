<script>
    if ('serviceWorker' in navigator) {
        window.addEventListener('load', () => {
            navigator.serviceWorker.register("{{ asset('service-worker.js') }}")
                .then(registration => {
                    console.log('Service Worker registered:', registration);
                })
                .catch(error => {
                    console.log('Service Worker registration failed:', error);
                });
        });
    }
    @if ((restaurant() && restaurant()->is_pwa_install_alert_show == 1) || (!restaurant() && global_setting()->is_pwa_install_alert_show == 1))
        const isIOS = /iPhone|iPad|iPod/i.test(navigator.userAgent);
        const isAndroid = /Android/i.test(navigator.userAgent);
        const isInStandaloneMode = ('standalone' in window.navigator) && window.navigator.standalone;

        window.addEventListener('beforeinstallprompt', (event) => {
            event.preventDefault();
            deferredPrompt = event;
            setTimeout(() => {
                if (isAndroid) {
                    deferredPrompt.prompt(); // This triggers the install prompt
                    deferredPrompt.userChoice.then((choiceResult) => {
                        if (choiceResult.outcome === 'accepted') {
                            console.log('User accepted the A2HS prompt');
                        } else {
                            console.log('User dismissed the A2HS prompt');
                        }
                        deferredPrompt = null;
                    });
                }
            }, 1000);
        });


        if (isIOS && !isInStandaloneMode) {
            // Show instruction only once, on scroll or click
            const showInstructionsOnce = () => {
                showIOSInstallInstructions();
                window.removeEventListener('scroll', showInstructionsOnce);
                window.removeEventListener('click', showInstructionsOnce);
            };

            window.addEventListener('scroll', showInstructionsOnce, { once: true });
            window.addEventListener('click', showInstructionsOnce, { once: true });
        }
        // ---------------------- Function to Show iOS Instructions ----------------------

        function showIOSInstallInstructions() {
            if (document.getElementById('iosInstallInstructions')) return;

            const instructions = document.createElement('div');
            instructions.id = 'iosInstallInstructions';
            instructions.innerHTML = `
                <div style="position: fixed; bottom: 10px; left: 10px; right: 10px; background: #fff; padding: 10px; border: 1px solid #ccc; border-radius: 5px; text-align: center; z-index: 1000;">
                    <p class="flex relative">@lang('messages.installAppInstruction')
                        <img class="absolute right-0 left-auto mr-5" src="{{ asset('img/share-ios.svg') }}" alt="Share Icon" style="width: 20px; vertical-align: middle;">

                    </p>
                    @lang('messages.addToHomeScreen').
                    <button id="closeInstructions" class="block text-center mx-auto" style="margin-top: 10px; padding: 5px 10px;">@lang('app.close')</button>
                </div>
            `;
            document.body.appendChild(instructions);

            document.getElementById('closeInstructions').addEventListener('click', function () {
                document.getElementById('iosInstallInstructions').remove();
            });
        }
    @endif
</script>
