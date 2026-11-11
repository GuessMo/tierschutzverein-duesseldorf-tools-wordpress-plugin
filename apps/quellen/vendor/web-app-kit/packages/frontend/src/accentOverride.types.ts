export interface ThemeVariableValues {
  light: string;
  dark: string;
}

export type ThemeVariableOverrides = Record<`--${string}`, ThemeVariableValues>;
