import numeral from 'numeral';
import 'numeral/locales';

export const formataValorSolicitadoTotal = (table) => {
  const soma = numeral();

  Object.entries(table).forEach(([, cell]) => {
    if (cell.vlSolicitado !== undefined) {
      soma.add(parseFloat(cell.vlSolicitado));
    }
  });

  numeral.locale('pt-br');
  numeral.defaultFormat('0,0.00');

  return soma.format();
};

export const converterStringParaClasseCss = (text) => {
  const classeCss = text
    .toString()
    .toLowerCase()
    .trim()
    .replace(/&/g, '-and-')
    .replace(/[\s\W-]+/g, '-');

  return classeCss;
};

export const formataValorAprovadoTotal = (table) => {
  const soma = numeral();

  Object.entries(table).forEach(([, cell]) => {
    if (typeof cell.vlAprovado !== 'undefined') {
      if (cell.tpAcao && cell.tpAcao === 'E') {
        return;
      }
      soma.add(parseFloat(cell.vlAprovado));
    }
  });

  return soma.format();
};

export const converterParaReal = (value) => {
  const parsedValue = parseFloat(value);
  return numeral(parsedValue).format('0,0.00');
};
