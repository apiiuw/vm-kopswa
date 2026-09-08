import Swal from 'sweetalert2';
import Chart from 'chart.js/auto';
import { createIcons, icons } from 'lucide';

window.Swal = Swal;
window.Chart = Chart;
window.createIcons = createIcons;
window.lucideIcons = icons;

// Initialize Lucide icons on DOM ready
document.addEventListener('DOMContentLoaded', () => {
    createIcons({ icons });
});
