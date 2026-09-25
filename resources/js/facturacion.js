export default function movimientoFacturacion() {
    return {
        periodos: {}, seleccionado: '', tipo: 'TRANSFERENCIA',
        init() {
            this.periodos = JSON.parse(this.$el.dataset.periodos || '{}');
            this.seleccionado = this.$el.dataset.seleccionado || '';
            this.tipo = this.$el.dataset.tipo || 'TRANSFERENCIA';
        },
        get resumen() { return this.periodos[this.seleccionado] || null; },
        get requiereDescripcion() { return ['INTERCAMBIO', 'CONDONACION'].includes(this.tipo); },
        get etiquetaDescripcion() {
            if (this.tipo === 'INTERCAMBIO') return 'Descripción del intercambio *';
            if (this.tipo === 'CONDONACION') return 'Motivo de la condonación *';
            return 'Observación (opcional)';
        },
        moneda(valor) {
            return 'RD$ ' + Number(valor || 0).toLocaleString('es-DO', {minimumFractionDigits: 2, maximumFractionDigits: 2});
        },
    };
}
