
import { NextRequest, NextResponse } from "next/server";
import { prisma } from "@/lib/prisma";

type RouteContext = {
  params: Promise<{ id: string }>;
};

// Edita um serviço
export async function PUT(
  request: NextRequest,
  context: RouteContext
) {
  try {
    const { id } = await context.params;
    const idNumerico = Number(id);

    if (!Number.isInteger(idNumerico) || idNumerico <= 0) {
      return NextResponse.json(
        { erro: "ID de serviço inválido." },
        { status: 400 }
      );
    }

    const body = await request.json();

    if (!body || typeof body !== "object" || Array.isArray(body)) {
      return NextResponse.json(
        { erro: "Dados do serviço inválidos." },
        { status: 400 }
      );
    }

    const nome =
      typeof body.nome === "string" ? body.nome.trim() : "";

    const descricao =
      typeof body.descricao === "string"
        ? body.descricao.trim()
        : "";

    const duracaoMinutos = body.duracaoMinutos;

    if (!nome) {
      return NextResponse.json(
        { erro: "O nome do serviço é obrigatório." },
        { status: 400 }
      );
    }

    if (nome.length > 120 || descricao.length > 1000) {
      return NextResponse.json(
        { erro: "Os dados ultrapassam o limite permitido." },
        { status: 400 }
      );
    }

    if (
      !Number.isInteger(duracaoMinutos) ||
      duracaoMinutos < 5 ||
      duracaoMinutos > 480
    ) {
      return NextResponse.json(
        {
          erro:
            "A duração deve ser um número inteiro entre 5 e 480 minutos.",
        },
        { status: 400 }
      );
    }

    const existente = await prisma.servico.findUnique({
      where: { id: idNumerico },
    });

    if (!existente) {
      return NextResponse.json(
        { erro: "Serviço não encontrado." },
        { status: 404 }
      );
    }

    const servico = await prisma.servico.update({
      where: { id: idNumerico },
      data: {
        nome,
        descricao: descricao || null,
        duracaoMinutos,
      },
    });

    return NextResponse.json(servico);
  } catch (error) {
    console.error("Erro ao atualizar serviço:", error);

    return NextResponse.json(
      { erro: "Não foi possível atualizar o serviço." },
      { status: 500 }
    );
  }
}

// Exclui um serviço
export async function DELETE(
  _request: NextRequest,
  context: RouteContext
) {
  try {
    const { id } = await context.params;
    const idNumerico = Number(id);

    if (!Number.isInteger(idNumerico) || idNumerico <= 0) {
      return NextResponse.json(
        { erro: "ID de serviço inválido." },
        { status: 400 }
      );
    }

    const existente = await prisma.servico.findUnique({
      where: { id: idNumerico },
    });

    if (!existente) {
      return NextResponse.json(
        { erro: "Serviço não encontrado." },
        { status: 404 }
      );
    }

    await prisma.servico.delete({
      where: { id: idNumerico },
    });

    return NextResponse.json({
      mensagem: "Serviço excluído com sucesso.",
    });
  } catch (error) {
    console.error("Erro ao excluir serviço:", error);

    return NextResponse.json(
      { erro: "Não foi possível excluir o serviço. Verifique se existem agendamentos vinculados." },
      { status: 500 }
    );
  }
}
