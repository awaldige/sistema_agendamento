
import "server-only";

import { SignJWT, jwtVerify } from "jose";
import { cookies } from "next/headers";

const COOKIE_NAME = "agendapro_session";
const SESSION_DURATION = 60 * 60 * 8;

function getSecret() {
  const secret = process.env.AUTH_SECRET;

  if (!secret || secret.length < 32) {
    throw new Error("AUTH_SECRET não configurada corretamente.");
  }

  return new TextEncoder().encode(secret);
}

export interface DemoSession {
  username: string;
  role: "admin" | "viewer";
}

export async function createSession(
  username: string,
  role: "admin" | "viewer"
) {
  const token = await new SignJWT({
    username,
    role,
  })
    .setProtectedHeader({ alg: "HS256" })
    .setIssuedAt()
    .setExpirationTime(`${SESSION_DURATION}s`)
    .sign(getSecret());

  const cookieStore = await cookies();

  cookieStore.set(COOKIE_NAME, token, {
    httpOnly: true,
    secure: process.env.NODE_ENV === "production",
    sameSite: "lax",
    path: "/",
    maxAge: SESSION_DURATION,
  });
}

export async function getSession(): Promise<DemoSession | null> {
  const cookieStore = await cookies();
  const token = cookieStore.get(COOKIE_NAME)?.value;

  if (!token) {
    return null;
  }

  try {
    const { payload } = await jwtVerify(token, getSecret());

    if (
      typeof payload.username !== "string" ||
      (payload.role !== "admin" && payload.role !== "viewer")
    ) {
      return null;
    }

    const expectedUsername =
      payload.role === "admin"
        ? process.env.ADMIN_USERNAME
        : process.env.DEMO_USERNAME;

    if (!expectedUsername || payload.username !== expectedUsername) {
      return null;
    }

    return {
      username: payload.username,
      role: payload.role,
    };
  } catch {
    return null;
  }
}

export async function isAdmin(): Promise<boolean> {
  const session = await getSession();
  return session?.role === "admin";
}

export async function destroySession() {
  const cookieStore = await cookies();

  cookieStore.set(COOKIE_NAME, "", {
    httpOnly: true,
    secure: process.env.NODE_ENV === "production",
    sameSite: "lax",
    path: "/",
    maxAge: 0,
  });
}
