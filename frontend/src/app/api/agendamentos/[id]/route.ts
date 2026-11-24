import { NextResponse } from "next/server";
import { prisma } from "@/lib/prisma";

type RouteContext = {
  params: Promise<{ id: string }>;
};

function validarData(data: string) {
  if (!/^\d{4}-\d{2}-\d{2}$/.test(data)) {
    return null;
  }

  const resultado = new Date(`${data}T12:00:00.000Z`);

  if (
    Number.isNaN(resultado.getTime()) ||
    resultado.toISOString().slice(0, 10) !== data
  ) {
    return null;
  }

  return resultado;
}

export async function PUT(
  request: Request,
  { params }: RouteContext
) {
  try {
    const { id } = await params;
    const agendamentoId = Number(id);

    if (!Number.isInteger(agendamentoId) || agendamentoId <= 0) {
      return NextResponse.json(
        { erro: "ID de agendamento inválido." },
        { status: 400 }
      );
    }

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

    if (
      paciente.length > 120 ||
      email.length > 150 ||
      telefone.length > 20 ||
      observacoes.length > 1000
    ) {
      return NextResponse.json(
        { erro: "Um ou mais campos excedem o limite permitido." },
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

    const dataObjeto = validarData(data);

    if (!dataObjeto) {
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

    const existente = await prisma.agendamento.findUnique({
      where: { id: agendamentoId },
    });

    if (!existente) {
      return NextResponse.json(
        { erro: "Agendamento não encontrado." },
        { status: 404 }
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

    // Calcula o intervalo do agendamento que está sendo editado.
    const [horaInicio, minutoInicio] = hora.split(":").map(Number);
    const novoInicio = horaInicio * 60 + minutoInicio;
    const novoFim = novoInicio + servico.duracaoMinutos;

    // Busca outros agendamentos na mesma data,
    // excluindo o próprio agendamento que está sendo editado.
    const agendamentosDoDia = await prisma.agendamento.findMany({
      where: {
        data: dataObjeto,
        id: {
          not: agendamentoId,
        },
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

    // Verifica sobreposição de horários.
    const conflito = agendamentosDoDia.find((item) => {
      const [horaExistente, minutoExistente] = item.hora
        .split(":")
        .map(Number);

      const inicioExistente = horaExistente * 60 + minutoExistente;
      const fimExistente =
        inicioExistente + item.servico.duracaoMinutos;

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


    const agendamento = await prisma.agendamento.update({
      where: { id: agendamentoId },
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
          },
        },
      },
    });

    return NextResponse.json(agendamento);
  } catch (error) {
    console.error("Erro ao atualizar agendamento:", error);

    return NextResponse.json(
      { erro: "Não foi possível atualizar o agendamento." },
      { status: 500 }
    );
  }
}

export async function DELETE(
  _request: Request,
  { params }: RouteContext
) {
  try {
    const { id } = await params;
    const agendamentoId = Number(id);

    if (!Number.isInteger(agendamentoId) || agendamentoId <= 0) {
      return NextResponse.json(
        { erro: "ID de agendamento inválido." },
        { status: 400 }
      );
    }

    const existente = await prisma.agendamento.findUnique({
      where: { id: agendamentoId },
    });

    if (!existente) {
      return NextResponse.json(
        { erro: "Agendamento não encontrado." },
        { status: 404 }
      );
    }

    await prisma.agendamento.delete({
      where: { id: agendamentoId },
    });

    return NextResponse.json({
      mensagem: "Agendamento excluído com sucesso.",
    });
  } catch (error) {
    console.error("Erro ao excluir agendamento:", error);

    return NextResponse.json(
      { erro: "Não foi possível excluir o agendamento." },
      { status: 500 }
    );
  }
}