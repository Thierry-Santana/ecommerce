(function () {
  function formatarCPF(valor) {
    const digits = (valor || '').replace(/\D/g, '').slice(0, 11);

    if (digits.length <= 3) return digits;
    if (digits.length <= 6) return `${digits.slice(0, 3)}.${digits.slice(3)}`;
    if (digits.length <= 9) return `${digits.slice(0, 3)}.${digits.slice(3, 6)}.${digits.slice(6)}`;

    return `${digits.slice(0, 3)}.${digits.slice(3, 6)}.${digits.slice(6, 9)}-${digits.slice(9, 11)}`;
  }

  function formatarTelefone(valor) {
    const digits = (valor || '').replace(/\D/g, '').slice(0, 11);
    if (!digits) return '';

    if (digits.length <= 10) {
      return digits.replace(/(\d{2})(\d{4})(\d{0,4})/, (_, ddd, prefix, suffix) => {
        return suffix ? `(${ddd}) ${prefix}-${suffix}` : `(${ddd}) ${prefix}`;
      });
    }

    return digits.replace(/(\d{2})(\d{5})(\d{0,4})/, (_, ddd, prefix, suffix) => {
      return suffix ? `(${ddd}) ${prefix}-${suffix}` : `(${ddd}) ${prefix}`;
    });
  }

  function aplicarMascaraAoCampo(campo, formatter) {
    if (!campo) return;

    const aplicar = () => {
      campo.value = formatter(campo.value);
    };

    campo.removeEventListener('input', aplicar);
    campo.addEventListener('input', aplicar);

    if (campo.value) {
      aplicar();
    }
  }

  aplicarMascaraAoCampo(document.getElementById('cpf'), formatarCPF);
  aplicarMascaraAoCampo(document.getElementById('telefone'), formatarTelefone);
})();
