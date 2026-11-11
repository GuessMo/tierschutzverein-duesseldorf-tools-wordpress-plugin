export interface NavLink {
  label: string;
  href: string;
}

export interface NavSection {
  id: string;
  label: string;
  links: NavLink[];
}
