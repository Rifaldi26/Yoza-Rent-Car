import './bootstrap';
import Alpine from 'alpinejs';
import { initNotificationBadges } from './components/notification-badge';

window.Alpine = Alpine;
Alpine.start();

document.addEventListener('DOMContentLoaded', initNotificationBadges);