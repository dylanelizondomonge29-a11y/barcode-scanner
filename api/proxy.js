// /api/proxy.js
export default async function handler(req, res) {
  const codigo = req.query.codigo || req.body?.codigo;
  if (!codigo) {
    return res.status(400).json({ error: true, mensaje: "Falta el parámetro 'codigo'" });
  }

  const apiUrl = `http://62.146.226.238/codigos/codigo_barras.php?api_key=123456&codigo_barras=${encodeURIComponent(codigo)}`;

  try {
    const response = await fetch(apiUrl);
    const data = await response.json();
    res.status(200).json(data);
  } catch (error) {
    res.status(500).json({
      error: true,
      mensaje: "No se pudo conectar con la API externa",
      detalle: error.message,
    });
  }
}
