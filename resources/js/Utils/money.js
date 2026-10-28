// "₹1,250" / "₹1,250.50" in Indian grouping.
export function formatCurrency(amount) {
    const value = Number(amount) || 0;
    return '₹' + value.toLocaleString('en-IN', { minimumFractionDigits: 0, maximumFractionDigits: 2 });
}
