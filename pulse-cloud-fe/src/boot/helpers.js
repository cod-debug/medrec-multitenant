import { boot } from "quasar/wrappers";

const helpers = {
    app_environment: process.env.VUE_APP_ENVIRONMENT || 'dev',
    environments_allowed_debugging: ['dev', 'development', 'testing', 'test', 'staging'],
    showDebugMessage(message) {
        const allow_debug = this.environments_allowed_debugging.includes(this.app_environment);
        if (allow_debug) {
            console.log(message);
        }
    },
    formatUserRole(loa) {
        switch (loa) {
            case 1:
                return 'Doctor';
            case 2:
                return 'Secretary';
            default:
                return 'Unknown';
        }
    },
    getAge(date_of_birth) {
        const today = new Date();
        const birthDate = new Date(date_of_birth);
        let age = today.getFullYear() - birthDate.getFullYear();
        const monthDifference = today.getMonth() - birthDate.getMonth();
        if (monthDifference < 0 || (monthDifference === 0 && today.getDate() < birthDate.getDate())) {
            age--;
        }
        return age;
    },
    formatDate(date_string, formatOption = { year: 'numeric', month: 'long', day: 'numeric' }) {
        const options = formatOption;
        return new Date(date_string).toLocaleDateString(undefined, options);
    },
    formatDateTime(date_string, formatOption = { year: 'numeric', month: 'long', day: 'numeric', hour: '2-digit', minute: '2-digit' }) {
        const options = formatOption;
        return new Date(date_string).toLocaleDateString(undefined, options).replaceAll(' at', '');
    },
    formatVisitStatus(status) {
        switch (status) {
            case 'queued':
                return {
                    color: 'blue',
                    label: 'Queued'
                }
            case 'checking':
                return {
                    color: 'orange',
                    label: 'Checking'
                }
            case 'for-payment':
                return {
                    color: 'purple',
                    label: 'For Payment'
                }
            case 'settled':
                return {
                    color: 'green-6',
                    label: 'Settled'
                }
            case 'cancelled':
                return {
                    color: 'red',
                    label: 'Cancelled'
                }
            default:
                return {
                    color: 'grey',
                    label: 'Unknown'
                }
        }
    },
    formatCurrency(amount) {
        return new Intl.NumberFormat('en-PH', { style: 'currency', currency: 'PHP' }).format(amount).replace('₱', '₱ ');
    },
    formatAdmissionStatus(status) {
        switch (status) {
            case 'admitted':
                return {
                    color: 'blue',
                    label: 'Admitted'
                }
            case 'discharged':
                return {
                    color: 'green-6',
                    label: 'Discharged'
                }
            default:
                return {
                    color: 'grey',
                    label: 'Unknown'
                }
        }
    },
    base64ToFile(base64, filename) {
        const arr = base64.split(',')
        const mime = arr[0].match(/:(.*?);/)[1]
        const bstr = atob(arr[1])
        let n = bstr.length
        const u8arr = new Uint8Array(n)

        while (n--) {
            u8arr[n] = bstr.charCodeAt(n)
        }

        return new File([u8arr], filename, { type: mime })
    },
    handlePrint(selector) {
        const content = document.querySelector(selector).innerHTML;
        const printWindow = window.open('', '', 'width=800,height=600');

        // Get all stylesheets from the parent window
        let styles = '';
        for (let i = 0; i < document.styleSheets.length; i++) {
            if (document.styleSheets[i].href) {
                styles += '<link rel="stylesheet" href="' + document.styleSheets[i].href + '">';
            }
        }

        // Get all inline styles
        const inlineStyles = document.querySelectorAll('style');
        inlineStyles.forEach(function (style) {
            styles += '<style>' + style.innerHTML + '</style>';
        });

        const html = '<html><head><title>Print</title>' + styles + '</head><body>' + content + '</body></html>';
        printWindow.document.open();
        printWindow.document.write(html);
        printWindow.document.close();

        const closePrintWindow = () => {
            if (!printWindow.closed) {
                printWindow.close();
            }
        };

        // Wait for styles to load before printing
        printWindow.onload = function () {
            printWindow.focus();
            printWindow.print();

            // Fires after print OR cancel
            printWindow.onafterprint = closePrintWindow;

            // Fallback (some browsers don’t trigger onafterprint)
            setTimeout(closePrintWindow, 10);
        };
    },
    nl2br (str, is_xhtml = false) {
        if (typeof str === 'undefined' || str === null) {
            return '';
        }
        let breakTag = (is_xhtml || typeof is_xhtml === 'undefined') ? '<br />' : '<br>';
        return (str + '').replaceAll(/([^>\r\n]?)(\r\n|\n\r|\r|\n)/g, '$1' + breakTag + '$2');
    }
}

export default boot(({ app }) => {
    app.config.globalProperties.$helpers = helpers
})