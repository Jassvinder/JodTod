import Swal from 'sweetalert2';

export function confirmAction({
    title = 'Are you sure?',
    text = '',
    confirmText = 'Yes',
    cancelText = 'Cancel',
    icon = 'warning',
    danger = false,
} = {}) {
    const isDark = document.documentElement.classList.contains('dark');

    return Swal.fire({
        title,
        text,
        icon,
        showCancelButton: true,
        confirmButtonText: confirmText,
        cancelButtonText: cancelText,
        confirmButtonColor: danger ? '#dc2626' : '#6366f1',
        cancelButtonColor: isDark ? '#4b5563' : '#9ca3af',
        background: isDark ? '#1f2937' : '#ffffff',
        color: isDark ? '#f3f4f6' : '#1f2937',
        customClass: {
            popup: 'rounded-xl shadow-xl',
            confirmButton: 'rounded-lg px-5 py-2.5 text-sm font-semibold',
            cancelButton: 'rounded-lg px-5 py-2.5 text-sm font-semibold',
        },
        reverseButtons: true,
    }).then((result) => result.isConfirmed);
}
