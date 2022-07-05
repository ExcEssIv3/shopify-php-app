import { Layout, Page } from '@shopify/polaris';
import { CustomerCard, ProductCard } from '../components';

export default function HomePage() {
  return (
    <Page>
      <Layout>
        <Layout.Section>
          <ProductCard></ProductCard>
          <CustomerCard></CustomerCard>
        </Layout.Section>
      </Layout>
    </Page>
  );
}
