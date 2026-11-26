
import { NextRequest, NextResponse } from "next/server";
import { jwtVerify } from "jose";

const COOKIE_NAME = "agendapro_session";

async function hasValidSession(token?: string) {
  const secret = process.env.AUTH_SECRET;

  if (!token || !secret || secret.length < 32) {
    return false;
  }

  try {
    const { payload } = await jwtVerify(
      token,
      new TextEncoder().encode(secret)
    );

    if (
      typeof payload.username !== "string" ||
      (payload.role !== "admin" && payload.role !== "viewer")
    ) {
      return false;
    }

    const expectedUsername =
      payload.role === "admin"
        ? process.env.ADMIN_USERNAME
        : process.env.DEMO_USERNAME;

    return Boolean(
      expectedUsername && payload.username === expectedUsername
    );
  } catch {
    return false;
  }
}

export async function proxy(request: NextRequest) {
  const pathname = request.nextUrl.pathname;
  const token = request.cookies.get(COOKIE_NAME)?.value;
  const authenticated = await hasValidSession(token);

  if (pathname === "/login") {
    if (authenticated) {
      return NextResponse.redirect(new URL("/", request.url));
    }

    return NextResponse.next();
  }

  if (!authenticated) {
    return NextResponse.redirect(new URL("/login", request.url));
  }

  return NextResponse.next();
}

export const config = {
  matcher: [
    "/((?!api|_next/static|_next/image|favicon.ico|.*\\.(?:svg|png|jpg|jpeg|gif|webp|ico)$).*)",
  ],
};
