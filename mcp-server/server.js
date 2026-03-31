const express = require("express");
const mysql = require("mysql2/promise");

const app = express();
app.use(express.json());

const PORT = Number(process.env.MCP_PORT || 3001);

const pool = mysql.createPool({
  host: process.env.DB_HOST || "localhost",
  user: process.env.DB_USER || "rootplt",
  password: process.env.DB_PASS || "PLT,./7788()__db",
  database: process.env.DB_NAME || "ihsanul-web",
  waitForConnections: true,
  connectionLimit: 10,
  queueLimit: 0,
});

const READONLY_TOOLS = {
  "get-users": {
    sql: "SELECT * FROM users ORDER BY 1 DESC LIMIT 200",
    params: () => [],
  },
  "get-menu": {
    sql: "SELECT * FROM menu_utama ORDER BY 1 ASC",
    params: () => [],
  },
  "get-artikel": {
    sql: "SELECT id_artikel, judul_artikel, kategori, penulis, tanggal_update, enable, viewer FROM artikel WHERE hapus = 1 ORDER BY tanggal_update DESC LIMIT ?",
    params: (body) => [Math.min(Math.max(Number(body.limit || 20), 1), 200)],
  },
};

async function runReadQuery(sql, params) {
  if (!/^\s*select\b/i.test(sql)) {
    throw new Error("Readonly mode: hanya query SELECT yang diizinkan.");
  }
  const [rows] = await pool.execute(sql, params);
  return rows;
}

app.get("/health", async (_req, res) => {
  try {
    await runReadQuery("SELECT 1 AS ok", []);
    res.json({ ok: true, mode: "readonly", port: PORT, db: "connected" });
  } catch (err) {
    res.status(500).json({ ok: false, mode: "readonly", error: err.message });
  }
});

app.get("/tool/list", (_req, res) => {
  res.json({ mode: "readonly", tools: Object.keys(READONLY_TOOLS) });
});

app.post("/tool/get-users", async (_req, res) => {
  try {
    const rows = await runReadQuery(READONLY_TOOLS["get-users"].sql, []);
    res.json(rows);
  } catch (err) {
    res.status(500).json({ error: err.message });
  }
});

app.post("/tool/read", async (req, res) => {
  const tool = String(req.body.tool || "").trim();
  const config = READONLY_TOOLS[tool];

  if (!config) {
    return res.status(400).json({
      error: "Tool tidak tersedia atau bukan readonly.",
      allowedTools: Object.keys(READONLY_TOOLS),
    });
  }

  try {
    const params = config.params(req.body || {});
    const rows = await runReadQuery(config.sql, params);
    return res.json(rows);
  } catch (err) {
    return res.status(500).json({ error: err.message });
  }
});

app.use("/tool", (_req, res) => {
  res.status(403).json({
    error: "MCP berjalan dalam mode readonly. Route ini tidak diizinkan.",
    allowedRoutes: ["POST /tool/get-users", "POST /tool/read", "GET /tool/list"],
  });
});

app.listen(PORT, () => {
  console.log(`MCP server readonly running at http://localhost:${PORT}`);
});
