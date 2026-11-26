
import { NextResponse } from "next/server";
import { createSession } from "@/lib/auth";

export async function POST(request: Request) {
  try {
    const body = await request.json();
    const { username, password } = body;

    if (typeof username !== "string" || typeof password !== "string") {
      return NextResponse.json(
        { erro: "Informe usuário e senha." },
        { status: 400 }
      );
    }

    let role: "admin" | "viewer" | null = null;

    // Acesso administrativo
    if (
      username === process.env.ADMIN_USERNAME &&
      password === process.env.ADMIN_PASSWORD
    ) {
      role = "admin";
    }
    // Acesso de visitante
    else if (
      username === process.env.DEMO_USERNAME &&
      password === process.env.DEMO_PASSWORD
    ) {
      role = "viewer";
    }

    if (!role) {
      return NextResponse.json(
        { erro: "Usuário ou senha incorretos." },
        { status: 401 }
      );
    }

    await createSession(username, role);

    return NextResponse.json({
      sucesso: true,
      role,
    });
  } catch (error) {
    console.error("Erro interno no login:", error);

    return NextResponse.json(
      { erro: "Não foi possível realizar o login." },
      { status: 500 }
    );
  }
}
