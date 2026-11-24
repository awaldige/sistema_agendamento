import "dotenv/config";
import { prisma } from "../src/lib/prisma";

async function popularServicos() {
  try {
    const quantidade = await prisma.servico.count();

    if (quantidade > 0) {
      console.log(
        `A tabela já possui ${quantidade} serviço(s). Nenhum registro foi inserido.`
      );
      return;
    }

    const servicos = [
      {
        nome: "Consulta inicial",
        descricao: "Primeira consulta e avaliação do paciente.",
      },
      {
        nome: "Consulta de retorno",
        descricao: "Acompanhamento e revisão da consulta anterior.",
      },
      {
        nome: "Avaliação",
        descricao: "Avaliação geral e orientação profissional.",
      },
      {
        nome: "Atendimento especializado",
        descricao: "Atendimento demonstrativo especializado.",
      },
    ];

    const resultado = await prisma.servico.createMany({
      data: servicos,
    });

    console.log(
      `${resultado.count} serviços fictícios cadastrados no Neon.`
    );
  } catch (error) {
    console.error("Erro ao inserir serviços demonstrativos:", error);
    process.exitCode = 1;
  } finally {
    await prisma.$disconnect();
  }
}

popularServicos();