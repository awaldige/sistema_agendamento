import { NextResponse } from "next/server";
import { destroySession } from "@/lib/auth";

export async function POST() {
  try {
    await destroySession();

    return NextResponse.json({ sucesso: true });
  } catch {
    return NextResponse.json(
      { erro: "Não foi possível encerrar a sessão." },
      { status: 500 }
    );
  }
}
