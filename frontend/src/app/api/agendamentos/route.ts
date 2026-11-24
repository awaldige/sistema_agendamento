
import { NextResponse } from "next/server";
import { prisma } from "@/lib/prisma";

export async function GET() {
  try {
    const agendamentos = await prisma.agendamento.findMany({
      include: {
        servico: {
          select: {
            id: true,
            nome: true,
            duracaoMinutos: true,
          },
        },
      },
      orderBy: [{ data: "asc" }, { hora: "asc" }],
    });

    return NextResponse.json(agendamentos);
  } catch (error) {
    console.error("Erro ao listar agendamentos:", error);

    return NextResponse.json(
      { erro: "Não foi possível listar os agendamentos." },
      { status: 500 }
    );
  }
}

export async function POST(request: Request) {
  try {
    const body = await request.json();

    const paciente =
      typeof body.paciente === "string" ? body.paciente.trim() : "";
    const email =
      typeof body.email === "string" ? body.email.trim() : "";
    const telefone =
      typeof body.telefone === "string" ? body.telefone.trim() : "";
    const data = typeof body.data === "string" ? body.data : "";
    const hora = typeof body.hora === "string" ? body.hora : "";
    const servicoId = Number(body.servicoId);
    const tipo = typeof body.tipo === "string" ? body.tipo : "";
    const observacoes =
      typeof body.observacoes === "string"
        ? body.observacoes.trim()
        : "";

    if (!paciente || !data || !hora || !servicoId || !tipo) {
      return NextResponse.json(
        { erro: "Preencha todos os campos obrigatórios." },
        { status: 400 }
      );
    }

    if (paciente.length > 120 || email.length > 150 || telefone.length > 20) {
      return NextResponse.json(
        { erro: "Um ou mais campos excedem o limite permitido." },
        { status: 400 }
      );
    }

    if (observacoes.length > 1000) {
      return NextResponse.json(
        { erro: "As observações devem ter no máximo 1000 caracteres." },
        { status: 400 }
      );
    }

    if (!Number.isInteger(servicoId) || servicoId <= 0) {
      return NextResponse.json(
        { erro: "Selecione um serviço válido." },
        { status: 400 }
      );
    }

    if (!["particular", "convenio"].includes(tipo)) {
      return NextResponse.json(
        { erro: "Tipo de atendimento inválido." },
        { status: 400 }
      );
    }

    if (!/^\d{4}-\d{2}-\d{2}$/.test(data)) {
      return NextResponse.json(
        { erro: "Data inválida." },
        { status: 400 }
      );
    }

    const dataObjeto = new Date(`${data}T12:00:00.000Z`);

    if (
      Number.isNaN(dataObjeto.getTime()) ||
      dataObjeto.toISOString().slice(0, 10) !== data
    ) {
      return NextResponse.json(
        { erro: "Data inválida." },
        { status: 400 }
      );
    }

    if (!/^([01]\d|2[0-3]):[0-5]\d$/.test(hora)) {
      return NextResponse.json(
        { erro: "Horário inválido." },
        { status: 400 }
      );
    }

    if (email && !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)) {
      return NextResponse.json(
        { erro: "Informe um e-mail válido." },
        { status: 400 }
      );
    }

    const servico = await prisma.servico.findUnique({
      where: { id: servicoId },
    });

    if (!servico) {
      return NextResponse.json(
        { erro: "O serviço selecionado não existe." },
        { status: 400 }
      );
    }

    // Calcula o intervalo do novo agendamento em minutos.
    const [horaInicio, minutoInicio] = hora.split(":").map(Number);
    const novoInicio = horaInicio * 60 + minutoInicio;
    const novoFim = novoInicio + servico.duracaoMinutos;

    // Busca os agendamentos existentes na mesma data.
    const agendamentosDoDia = await prisma.agendamento.findMany({
      where: {
        data: dataObjeto,
      },
      include: {
        servico: {
          select: {
            nome: true,
            duracaoMinutos: true,
          },
        },
      },
    });

    // Verifica se existe sobreposição de horários.
    const conflito = agendamentosDoDia.find((existente) => {
      const [horaExistente, minutoExistente] = existente.hora
        .split(":")
        .map(Number);

      const inicioExistente = horaExistente * 60 + minutoExistente;
      const fimExistente =
        inicioExistente + existente.servico.duracaoMinutos;

      return (
        novoInicio < fimExistente &&
        inicioExistente < novoFim
      );
    });

    if (conflito) {
      return NextResponse.json(
        {
          erro: `Conflito de horário! Já existe um agendamento para ${conflito.paciente}, às ${conflito.hora}, com o serviço "${conflito.servico.nome}".`,
        },
        { status: 409 }
      );
    }

    const agendamento = await prisma.agendamento.create({
      data: {
        paciente,
        email: email || null,
        telefone: telefone || null,
        data: dataObjeto,
        hora,
        servicoId,
        tipo,
        observacoes: observacoes || null,
      },
      include: {
        servico: {
          select: {
            id: true,
            nome: true,
            duracaoMinutos: true,
          },
        },
      },
    });

    return NextResponse.json(agendamento, { status: 201 });
  } catch (error) {
    console.error("Erro ao cadastrar agendamento:", error);

    return NextResponse.json(
      { erro: "Não foi possível cadastrar o agendamento." },
      { status: 500 }
    );
  }
}
