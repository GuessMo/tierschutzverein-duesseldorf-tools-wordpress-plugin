export interface MultiComboboxOption {
  value: string;
  label: string;
}

export interface MultiComboboxProps {
  id?: string;
  options: readonly MultiComboboxOption[];
  value: readonly string[];
  onValueChange: (value: string[]) => void;
  placeholder: string;
  searchPlaceholder: string;
  emptyText: string;
  selectedLabel: string;
  removeLabel: (label: string) => string;
  disabled?: boolean;
  className?: string;
  "aria-describedby"?: string;
  "aria-invalid"?: boolean;
}
