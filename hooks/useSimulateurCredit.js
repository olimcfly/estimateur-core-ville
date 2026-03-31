'use client';

import { useMemo } from 'react';

const toNumber = (value, fallback = 0) => {
  const parsed = Number(value);
  return Number.isFinite(parsed) ? parsed : fallback;
};

const calcMensualiteHorsAssurance = ({ capital, tauxAnnuel, dureeAnnees }) => {
  const mensualites = dureeAnnees * 12;
  if (!mensualites || capital <= 0) return 0;

  const tauxMensuel = tauxAnnuel / 12 / 100;
  if (tauxMensuel === 0) return capital / mensualites;

  return (capital * tauxMensuel) / (1 - (1 + tauxMensuel) ** -mensualites);
};

export default function useSimulateurCredit({
  prixBien = 300000,
  apport = 30000,
  dureeAnnees = 20,
  tauxInteret = 3.5,
  tauxAssurance = 0.35,
  salaireMensuel = 0,
}) {
  return useMemo(() => {
    const prix = Math.max(0, toNumber(prixBien));
    const apportPersonnel = Math.max(0, Math.min(prix, toNumber(apport)));
    const annees = Math.max(1, toNumber(dureeAnnees, 20));
    const taux = Math.max(0, toNumber(tauxInteret, 3.5));
    const assurance = Math.max(0, toNumber(tauxAssurance, 0.35));
    const salaire = Math.max(0, toNumber(salaireMensuel));

    const montantEmprunte = Math.max(0, prix - apportPersonnel);
    const nombreMensualites = annees * 12;

    const mensualiteCredit = calcMensualiteHorsAssurance({
      capital: montantEmprunte,
      tauxAnnuel: taux,
      dureeAnnees: annees,
    });

    const mensualiteAssurance = (montantEmprunte * (assurance / 100)) / 12;
    const mensualite = mensualiteCredit + mensualiteAssurance;

    let capitalRestant = montantEmprunte;
    const tauxMensuel = taux / 12 / 100;

    const tableauAmortissement = Array.from({ length: nombreMensualites }, (_, index) => {
      const interets = capitalRestant * tauxMensuel;
      const capital = Math.max(0, mensualiteCredit - interets);
      capitalRestant = Math.max(0, capitalRestant - capital);

      return {
        mois: index + 1,
        capital: Number(capital.toFixed(2)),
        interets: Number(interets.toFixed(2)),
        assurance: Number(mensualiteAssurance.toFixed(2)),
        mensualite: Number((capital + interets + mensualiteAssurance).toFixed(2)),
        resteDu: Number(capitalRestant.toFixed(2)),
      };
    });

    const totalInterets = tableauAmortissement.reduce((sum, line) => sum + line.interets, 0);
    const totalAssurance = mensualiteAssurance * nombreMensualites;
    const coutTotal = montantEmprunte + totalInterets + totalAssurance;

    const tauxEndettement = salaire
      ? Number(((mensualite / salaire) * 100).toFixed(2))
      : 0;

    return {
      montantEmprunte: Number(montantEmprunte.toFixed(2)),
      mensualite: Number(mensualite.toFixed(2)),
      coutTotal: Number(coutTotal.toFixed(2)),
      totalInterets: Number(totalInterets.toFixed(2)),
      totalAssurance: Number(totalAssurance.toFixed(2)),
      mensualiteCredit: Number(mensualiteCredit.toFixed(2)),
      tableauAmortissement,
      tauxEndettement,
      nombreMensualites,
    };
  }, [prixBien, apport, dureeAnnees, tauxInteret, tauxAssurance, salaireMensuel]);
}
