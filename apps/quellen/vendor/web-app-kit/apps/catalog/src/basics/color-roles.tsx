import { COLOR_ROLE_GROUPS } from "./color-roles.data";
import { RoleBox } from "./role-box";

export function ColorRoles() {
  return (
    <div className="flex flex-col gap-6">
      {COLOR_ROLE_GROUPS.map((group) => (
        <section
          key={group.id}
          aria-labelledby={`roles-${group.id}`}
          className="flex flex-col gap-2"
        >
          <h3 id={`roles-${group.id}`} className="text-sm font-medium">
            {group.title}
          </h3>
          <div className="grid grid-cols-[repeat(auto-fill,minmax(13rem,1fr))] gap-3">
            {group.roles.map((role) => (
              <RoleBox key={role.name} {...role} />
            ))}
          </div>
        </section>
      ))}
    </div>
  );
}
