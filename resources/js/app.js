import './bootstrap';
import 'notyf/notyf.min.css';
import { Notyf } from 'notyf';
import { EmojiButton } from '@joeattardi/emoji-button';

window.EmojiButton = EmojiButton;

// Initialize Notyf globally
window.notyf = new Notyf({
    duration: 4000,
    position: { x: 'right', y: 'top' },
    dismissible: true
});
