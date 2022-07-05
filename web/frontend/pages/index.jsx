import { Layout, Page } from '@shopify/polaris';
import { CustomerCard, ProductCard } from '../components';
import { useAppQuery } from '../hooks';


export default function HomePage() {
  // useAppQuery({
  //   // url: 'api/customers/update'
  //   url: 'api/auth'
  // });
  
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
