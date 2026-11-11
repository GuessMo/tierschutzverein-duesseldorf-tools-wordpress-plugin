function findVariable(scope, name) {
  for (let current = scope; current; current = current.upper) {
    const variable = current.set.get(name);
    if (variable) return variable;
  }
  return undefined;
}

function getImportedName(specifier) {
  if (specifier.type === "ImportDefaultSpecifier") return "default";
  if (specifier.type === "ImportNamespaceSpecifier") return "*";
  return specifier.imported?.name ?? specifier.imported?.value;
}

function isConstantDefinition(definition) {
  return (
    definition.type === "Variable" &&
    definition.parent.kind === "const" &&
    definition.node.id.type === "Identifier" &&
    definition.node.init !== null
  );
}

function describeImport(definition, kitModules) {
  const source = definition.parent?.source?.value;
  if (typeof source !== "string") return { kind: "unknown" };
  const importedName = getImportedName(definition.node);
  return { kind: kitModules.includes(source) ? "kit" : "import", importedName };
}

export function lookupBinding(identifier, sourceCode, kitModules) {
  const variable = findVariable(sourceCode.getScope(identifier), identifier.name);
  const definition = variable?.defs[0];
  if (!definition) return { kind: "unknown" };
  if (definition.type === "ImportBinding") return describeImport(definition, kitModules);
  if (!isConstantDefinition(definition)) return { kind: "unknown" };
  return { kind: "constant", variable, init: definition.node.init };
}
