import "dotenv/config";
import { prisma } from "../src/lib/prisma";

async function testarBanco() {
  try {
    const servicos = await prisma.servico.findMany({
      orderBy: { nome: "asc" },
    });

    console.log("\nConexão com Neon estabelecida!");
    console.log("Consulta à tabela servicos realizada com sucesso.");
    console.log("Quantidade de registros:", servicos.length);
    console.table(servicos);
  } catch (error) {
    console.error("Erro ao consultar o banco:", error);
    process.exitCode = 1;
  } finally {
    await prisma.$disconnect();
  }
}

testarBanco();
