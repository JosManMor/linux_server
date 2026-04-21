(() => {
  const contenido = document.getElementById("contenido");
  const chars = document.getElementById("chars");

  if (!contenido || !chars) {
    return;
  }

  const updateCount = () => {
    chars.textContent = `${contenido.value.length} caracteres`;
  };

  contenido.addEventListener("input", updateCount);
  updateCount();
})();
