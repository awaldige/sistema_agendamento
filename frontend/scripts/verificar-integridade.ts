
import "dotenv/config";
import { prisma } from "../src/lib/prisma";

async function verificarIntegridade() {
  try {
    const totalServicos = await prisma.servico.count();
    const totalAgendamentos = await prisma.agendamento.count();

    const agendamentos = await prisma.agendamento.findMany({
      select: {
        id: true,
        paciente: true,
        data: true,
        hora: true,
        servico: {
          select: {
            id: true,
            nome: true,
          },
        },
      },
      orderBy: [
        { data: "asc" },
        { hora: "asc" },
      ],
    });

    console.log("\n=== VERIFICAÇÃO DE INTEGRIDADE ===");
    console.log("Conexão com Neon: OK");
    console.log("Total de serviços:", totalServicos);
    console.log("Total de agendamentos:", totalAgendamentos);

    console.log("\n=== AGENDAMENTOS E SERVIÇOS ===");

    console.table(
      agendamentos.map((item) => ({
        ID: item.id,
        Paciente: item.paciente,
        Data: item.data.toISOString().slice(0, 10),
        Horário: item.hora,
        Serviço: item.servico.nome,
        ID_Serviço: item.servico.id,
      }))
    );

    console.log("\nRelacionamentos com serviços: OK");
    console.log("Verificação concluída!");
  } catch (error) {
    console.error("\nErro na verificação:", error);
    process.exitCode = 1;
  } finally {
    await prisma.$disconnect();
  }
}

verificarIntegridade();
