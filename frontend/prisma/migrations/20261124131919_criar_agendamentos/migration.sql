-- CreateTable
CREATE TABLE "agendamentos" (
    "id" SERIAL NOT NULL,
    "paciente" VARCHAR(120) NOT NULL,
    "email" VARCHAR(150),
    "telefone" VARCHAR(20),
    "data" DATE NOT NULL,
    "hora" VARCHAR(5) NOT NULL,
    "servico_id" INTEGER NOT NULL,
    "tipo" VARCHAR(20) NOT NULL,
    "observacoes" TEXT,
    "created_at" TIMESTAMP(3) NOT NULL DEFAULT CURRENT_TIMESTAMP,

    CONSTRAINT "agendamentos_pkey" PRIMARY KEY ("id")
);

-- CreateIndex
CREATE INDEX "agendamentos_data_hora_idx" ON "agendamentos"("data", "hora");

-- CreateIndex
CREATE INDEX "agendamentos_servico_id_idx" ON "agendamentos"("servico_id");

-- AddForeignKey
ALTER TABLE "agendamentos" ADD CONSTRAINT "agendamentos_servico_id_fkey" FOREIGN KEY ("servico_id") REFERENCES "servicos"("id") ON DELETE RESTRICT ON UPDATE CASCADE;
