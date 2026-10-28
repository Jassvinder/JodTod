// Same rules as the mobile app: 10-digit numbers are treated as Indian.
export function toWhatsAppNumber(phone) {
    const digits = String(phone || '').replace(/\D/g, '');
    if (digits.length === 10) return `91${digits}`;
    if (digits.length === 11 && digits.startsWith('0')) return `91${digits.slice(1)}`;
    return digits.length > 10 ? digits : null;
}

// wa.me link to the person's chat (or WhatsApp's contact picker without a number), with the message typed in.
export function whatsAppLink(phone, message = '') {
    const number = toWhatsAppNumber(phone);
    const text = message ? `text=${encodeURIComponent(message)}` : '';
    if (number) return `https://wa.me/${number}${text ? `?${text}` : ''}`;
    return `https://wa.me/?${text}`;
}

export function telLink(phone) {
    return `tel:${String(phone || '').replace(/[^\d+]/g, '')}`;
}
