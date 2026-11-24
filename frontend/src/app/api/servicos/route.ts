
import { NextRequest, NextResponse } from "next/server";
import { prisma } from "@/lib/prisma";

// Lista todos os serviços
export async function GET() {
  try {
    const servicos = await prisma.servico.findMany({
      orderBy: { nome: "asc" },
    });

    return NextResponse.json(servicos);
  } catch (error) {
    console.error("Erro ao listar serviços:", error);

    return NextResponse.json(
      { erro: "Não foi possível carregar os serviços." },
      { status: 500 }
    );
  }
}

// Cadastra um novo serviço
export async function POST(request: NextRequest) {
  try {
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
        { erro: "A duração deve ser um número inteiro entre 5 e 480 minutos." },
        { status: 400 }
      );
    }

    const servico = await prisma.servico.create({
      data: {
        nome,
        descricao: descricao || null,
        duracaoMinutos,
      },
    });

    return NextResponse.json(servico, { status: 201 });
  } catch (error) {
    console.error("Erro ao cadastrar serviço:", error);

    return NextResponse.json(
      { erro: "Não foi possível cadastrar o serviço." },
      { status: 500 }
    );
  }
}
